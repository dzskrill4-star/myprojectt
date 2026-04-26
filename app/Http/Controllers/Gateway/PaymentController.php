<?php

namespace App\Http\Controllers\Gateway;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Models\AdminNotification;
use App\Models\BadgeReward;
use App\Models\Deposit;
use App\Models\GatewayCurrency;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserCoinBalance;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller {

    public function payment($id) {
        try {
            $orderId = decrypt($id);
        } catch (\Throwable $th) {
            abort(404);
        }

        $order = Order::unpaid()->findOrFail($orderId);

        $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })->with('method')->orderby('method_code')->get();

        $pageTitle = 'Payment Methods';

        if ($order->created_at < now()->subHours(2)) {
            $notify[] = ['error', 'This order is expired. Please create a new order.'];
            return to_route('plans')->withNotify($notify);
        }

        return view('Template::user.payment.payment', compact('gatewayCurrency', 'pageTitle', 'order'));
    }

    public function deposit() {
        $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })->with('method')->orderby('name')->get();
        $pageTitle = 'Deposit Money';
        return view('Template::user.payment.deposit', compact('gatewayCurrency', 'pageTitle'));
    }

    private function getGateway($request, $amount) {
        $gateway   = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })->where('method_code', $request->gateway)->where('currency', $request->currency)->first();

        if (!$gateway) {
            throw ValidationException::withMessages(['error' => 'Invalid gateway']);
        }

        // Enforce visibility rules server-side: check users.baridi column
        $name = strtolower(str_replace(' ', '', $gateway->name ?? ''));
        $isBaridi = $name === 'baridimob';
        $isUSDT = strtolower($gateway->name ?? '') === 'usdt';

        if (auth()->check()) {
            $user = auth()->user();
            $userBaridiEnabled = $user->baridi == 1;
            
            if (!$userBaridiEnabled) {
                if (!$isUSDT) {
                    throw ValidationException::withMessages(['error' => 'Invalid gateway']);
                }
            } else {
                if (!($isUSDT || $isBaridi)) {
                    throw ValidationException::withMessages(['error' => 'Invalid gateway']);
                }
            }
        }

        if ($gateway->min_amount > $amount) {
            throw ValidationException::withMessages(['error' => 'Minimum limit for this gateway is ' . $gateway->min_amount]);
        }

        if ($gateway->min_amount > $amount) {
            throw ValidationException::withMessages(['error' => 'Maximum limit for this gateway is ' . $gateway->max_amount]);
        }

        return $gateway;
    }

    public function submitPayment(Request $request, $id) {
        $request->validate([
            'gateway'  => 'required',
            'currency' => 'required',
        ]);

        $order  = Order::where('id', $id)->where('user_id', auth()->id())->unpaid()->firstOrFail();
        $amount = $order->amount;

        if ($order->created_at < now()->subHours(2)) {
            $notify[] = ['error', 'This order is expired. Please create a new order.'];
            return to_route('plans')->withNotify($notify);
        }

        return $this->depositInsert($amount, $this->getGateway($request, $amount), $order);
    }

    public function submitDeposit(Request $request) {
        $request->validate([
            'amount'   => 'required|numeric|gt:0',
            'gateway'  => 'required',
            'currency' => 'required',
        ]);

        return $this->depositInsert($request->amount, $this->getGateway($request, $request->amount));
    }

    public function depositInsert($amount, $gateway, $order = null) {
        $charge      = $gateway->fixed_charge + ($amount * $gateway->percent_charge / 100);
        $payable     = $amount + $charge;

        // Enforce fixed rate 1 USD = 250 DZD for Baridi Mob
        $name = strtolower(str_replace(' ', '', $gateway->name ?? ''));
        $isBaridi = $name === 'baridimob' || strtoupper($gateway->currency) === 'DZD';
        $rate = $isBaridi ? 250 : $gateway->rate;
        $finalAmount = $payable * $rate;

        $deposit                  = new Deposit();
        $deposit->user_id         = auth()->id();
        $deposit->order_id        = $order ? $order->id : 0;
        $deposit->method_code     = $gateway->method_code;
        $deposit->method_currency = strtoupper($gateway->currency);
        $deposit->amount          = $amount;
        $deposit->charge          = $charge;
        $deposit->rate            = $rate;
        $deposit->final_amount    = $finalAmount;
        $deposit->btc_amount      = 0;
        $deposit->btc_wallet      = "";
        $deposit->trx             = getTrx();
        $deposit->success_url     = $order ? route('user.plans.purchased') : route('user.deposit.history');
        $deposit->failed_url      = route('user.deposit.index');
        $deposit->save();
        session()->put('Track', $deposit->trx);
        return to_route('user.deposit.confirm');
    }

    public function appDepositConfirm($hash) {
        try {
            $id = decrypt($hash);
        } catch (\Exception $ex) {
            abort(404);
        }
        $deposit = Deposit::where('id', $id)->where('status', Status::PAYMENT_INITIATE)->orderBy('id', 'DESC')->firstOrFail();
        $user = User::findOrFail($deposit->user_id);
        auth()->login($user);
        session()->put('Track', $deposit->trx);
        return to_route('user.deposit.confirm');
    }

    public function depositConfirm() {
        $track = session()->get('Track');
        $deposit = Deposit::where('trx', $track)->orderBy('id', 'DESC')->with('gateway')->firstOrFail();

        if ($deposit->method_code >= 1000) {
            return to_route('user.deposit.manual.confirm');
        }

        $dirName = $deposit->gateway->alias;
        $new = __NAMESPACE__ . '\\' . $dirName . '\\ProcessController';


        $data = $new::process($deposit);
        $data = json_decode($data);

        if (isset($data->error)) {
            $notify[] = ['error', $data->message];
            return back()->withNotify($notify);
        }
        if (isset($data->redirect)) {
            return redirect($data->redirect_url);
        }

        // for Stripe V3
        if (isset($data->session)){
            $deposit->btc_wallet = $data->session->id;
            $deposit->save();
        }

        $pageTitle = 'Confirm Payment';
        return view("Template::$data->view", compact('data', 'pageTitle', 'deposit'));
    }

    public static function userDataUpdate($deposit, $isManual = null) {
        if ($deposit->status == Status::PAYMENT_INITIATE || $deposit->status == Status::PAYMENT_PENDING) {
            $deposit->status = Status::PAYMENT_SUCCESS;
            $deposit->save();

            $user = User::find($deposit->user_id);
            $methodName = $deposit->methodName();

            $user->balance += $deposit->amount;
            $user->save();

            $transaction               = new Transaction();
            $transaction->user_id      = $user->id;
            $transaction->amount       = $deposit->amount;
            $transaction->post_balance = $user->balance;
            $transaction->charge       = $deposit->charge;
            $transaction->wallet_type  = Status::DEPOSIT_WALLET;
            $transaction->trx_type     = '+';
            $transaction->details      = 'Balance added to deposit wallet';
            $transaction->remark       = 'deposit';
            $transaction->trx          = $deposit->trx;
            $transaction->currency     = gs('cur_text');
            $transaction->save();

            if (!$isManual) {
                self::notifyAdmin($user, $methodName);
            }

            if ($deposit->order_id > 0) {
                self::handleOrder($deposit, $user, $methodName, $isManual);
            } else {
                notify($user, $isManual ? 'DEPOSIT_APPROVE' : 'DEPOSIT_COMPLETE', [
                    'method_name'     => $methodName,
                    'method_currency' => $deposit->method_currency,
                    'method_amount'   => showAmount($deposit->final_amount, currencyFormat: false),
                    'amount'          => showAmount($deposit->amount, currencyFormat: false),
                    'charge'          => showAmount($deposit->charge, currencyFormat: false),
                    'rate'            => showAmount($deposit->rate, currencyFormat: false),
                    'trx'             => $deposit->trx,
                    'post_balance'    => showAmount($user->balance)
                ]);
            }
        }
    }

    private static function handleOrder($deposit, $user, $methodName, $isManual) {

        $order = $deposit->order;

        $user = $order->user;
        $user->balance        -= $deposit->amount;
        $user->save();

        $order->status        = Status::ORDER_APPROVED;
        $order->save();

        session()->put('payment', true);

        UserCoinBalance::where('user_id', $user->id)->where('miner_id', $order->miner_id)->firstOrCreate([
            'user_id'  => $user->id,
            'miner_id' => $order->miner_id,
        ]);

        $referrer = $user->referrer;

        if (gs('referral_system') && $referrer) {
            levelCommission($user, $order->amount, $order->trx);
        }

        $transaction               = new Transaction();
        $transaction->user_id      = $user->id;
        $transaction->amount       = $order->amount;
        $transaction->post_balance = $user->balance;
        $transaction->charge       = $deposit->charge;
        $transaction->wallet_type  = Status::DEPOSIT_WALLET;
        $transaction->trx_type     = '-';
        $transaction->details      = 'New mining plan purchased';
        $transaction->remark       = 'payment';
        $transaction->trx          = $order->trx;
        $transaction->currency     = gs('cur_text');
        $transaction->save();

        if ($order->plan_details->user_badge_id && $order->plan_details->badge_discount) {
            $badgeReward                 = new BadgeReward();
            $badgeReward->user_id        = $order->user_id;
            $badgeReward->user_badge_id  = $order->plan_details->user_badge_id;
            $badgeReward->badge_id       = $order->plan_details->badge_id;
            $badgeReward->transaction_id = $transaction->id;
            $badgeReward->amount         = $order->plan_details->badge_discount;
            $badgeReward->currency       = $transaction->currency;
            $badgeReward->remark         = 'plan_price_discount';
            $badgeReward->save();
        }

        notify($user, $isManual ? 'PAYMENT_APPROVE' : 'PAYMENT_COMPLETE', [
            'plan_title'      => $order?->plan?->title ?? 'Unknown',
            'method_name'     => $methodName,
            'method_currency' => $deposit->method_currency,
            'method_amount'   => showAmount($deposit->final_amount, currencyFormat: false),
            'amount'          => showAmount($deposit->amount, currencyFormat: false),
            'charge'          => showAmount($deposit->charge, currencyFormat: false),
            'rate'            => showAmount($deposit->rate, currencyFormat: false),
            'trx'             => $deposit->trx,
            'post_balance'    => showAmount($user->balance)
        ]);
    }

    private static function notifyAdmin($user, $methodName, $isOrder = false) {
        $adminNotification            = new AdminNotification();
        $adminNotification->user_id   = $user->id;
        $adminNotification->title     = $isOrder ? 'Payment' : 'Deposit' . ' successful via ' . $methodName;
        $adminNotification->click_url = urlPath('admin.deposit.successful');
        $adminNotification->save();
    }

    public function manualDepositConfirm() {
        $track = session()->get('Track');
        $data = Deposit::with('gateway')->where('status', Status::PAYMENT_INITIATE)->where('trx', $track)->first();
        abort_if(!$data, 404);
        if ($data->method_code > 999) {
            $pageTitle = 'Confirm Payment';
            $method = $data->gatewayCurrency();
            $gateway = $method->method;
            return view('Template::user.payment.manual', compact('data', 'pageTitle', 'method', 'gateway'));
        }
        abort(404);
    }

    public function manualDepositUpdate(Request $request) {
        $track   = session()->get('Track');
        $deposit = Deposit::with('gateway', 'order')->where('status', Status::PAYMENT_INITIATE)->where('trx', $track)->first();

        if (!$deposit) {
            abort(404);
        }

        $order = $deposit->order;

        $gatewayCurrency = $deposit->gatewayCurrency();
        $gateway         = $gatewayCurrency->method;
        $formData        = $gateway->form->form_data;

        $formProcessor  = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);
        // Additional validation for Baridi Mob: require receipt_image
        $methodName = strtolower(str_replace(' ', '', $gatewayCurrency->name ?? ''));
        $isBaridi = $methodName === 'baridimob' || strtoupper($deposit->method_currency) === 'DZD';

        if ($isBaridi) {
            $validationRule['receipt_image'] = ['required', 'file', 'mimes:jpg,jpeg,png,pdf'];
        }

        $request->validate($validationRule);
        $userData = $formProcessor->processFormData($request, $formData);

        if ($isBaridi && $request->hasFile('receipt_image')) {
            $directory = date('Y') . '/' . date('m') . '/' . date('d');
            $path = getFilePath('depositVerify') . '/' . $directory;
            $stored = fileUploader($request->file('receipt_image'), $path);
            $userData[] = [
                'name' => 'receipt_image',
                'type' => 'file',
                'value' => $directory . '/' . $stored,
            ];
        }

        $deposit->detail = $userData;
        $deposit->status = Status::PAYMENT_PENDING; // pending
        $deposit->save();

        $adminNotification            = new AdminNotification();
        $adminNotification->user_id   = $deposit->user->id;
        $adminNotification->title     = 'Payment request form ' . $deposit->user->username;
        $adminNotification->click_url = urlPath('admin.deposit.details', $deposit->id);
        $adminNotification->save();

        if ($order) {
            $order->status = Status::ORDER_PENDING;
            $order->save();

            notify($deposit->user, 'PAYMENT_REQUEST', [
                'plan_title'     => $order->plan?->title ?? 'Unknown',
                'method_name'     => $deposit->gatewayCurrency()->name,
                'method_currency' => $deposit->method_currency,
                'method_amount'   => showAmount($deposit->final_amount),
                'amount'          => showAmount($deposit->amount),
                'charge'          => showAmount($deposit->charge),
                'rate'            => showAmount($deposit->rate),
                'trx'             => $deposit->trx,
            ]);

            $notify[] = ['success', 'Your payment request has been taken'];
            return to_route('user.deposit.history')->withNotify($notify);
        }

        notify($deposit->user, 'DEPOSIT_REQUEST', [
            'method_name' => $deposit->gatewayCurrency()->name,
            'method_currency' => $deposit->method_currency,
            'method_amount' => showAmount($deposit->final_amount, currencyFormat: false),
            'amount' => showAmount($deposit->amount, currencyFormat: false),
            'charge' => showAmount($deposit->charge, currencyFormat: false),
            'rate' => showAmount($deposit->rate, currencyFormat: false),
            'trx' => $deposit->trx
        ]);


$notify[] = ['success', __('manual.deposit_success')];
        return to_route('user.deposit.history')->withNotify($notify);
    }
}
