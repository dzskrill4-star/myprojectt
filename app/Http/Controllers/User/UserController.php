<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Lib\GoogleAuthenticator;
use App\Models\DeviceToken;
use App\Models\Form;
use App\Models\Order;
use App\Models\Referral;
use App\Models\ReferralLog;
use App\Models\Transaction;
use App\Models\UserCoinBalance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller {

    public function home() {
        $pageTitle               = 'Dashboard';
        $user                    = auth()->user();
        $transactions            = Transaction::where('user_id', $user->id)->orderBy('id', 'desc')->limit(6)->get();
        $totalReturnedAmount     =  $user->totalReturnedAmount();
        $totalReferralCommission = $user->totalReferralCommission();
        // Total earning is mining/investment only (referral commissions are separate)
        $totalEarning            = $totalReturnedAmount;
        return view('Template::user.dashboard', compact('pageTitle', 'transactions', 'user', 'totalReturnedAmount', 'totalReferralCommission', 'totalEarning'));
    }

    public function depositHistory(Request $request) {
        $pageTitle = 'All Deposits';
        $deposits  = auth()->user()->deposits()->searchable(['trx'])->with(['gateway'])->orderBy('id', 'desc')->paginate(getPaginate());
        return view('Template::user.payment_history', compact('pageTitle', 'deposits'));
    }

    public function orders(Request $request) {
        $pageTitle = 'All Orders';
        $orders    = Order::where('user_id', auth()->id())->where('status', '!=', Status::ORDER_UNPAID)->with('miner')->orderBy('id', 'desc')->paginate(getPaginate());
        return view('Template::user.orders', compact('pageTitle', 'orders'));
    }

    public function show2faForm() {
        $ga        = new GoogleAuthenticator();
        $user      = auth()->user();
        $secret    = $ga->createSecret();
        $qrCodeUrl = $ga->getQRCodeGoogleUrl($user->username . '@' . gs('site_name'), $secret);
        $pageTitle = '2FA Security';
        return view('Template::user.twofactor', compact('pageTitle', 'secret', 'qrCodeUrl'));
    }

    public function create2fa(Request $request) {
        $user = auth()->user();

        $request->validate([
            'key'  => 'required',
            'code' => 'required',
        ]);
        $response = verifyG2fa($user, $request->code, $request->key);
        if ($response) {
            $user->tsc = $request->key;
            $user->ts  = Status::ENABLE;
            $user->save();
            $notify[] = ['success', 'Two factor authenticator activated successfully'];
            return back()->withNotify($notify);
        } else {
            $notify[] = ['error', 'Wrong verification code'];
            return back()->withNotify($notify);
        }
    }

    public function disable2fa(Request $request) {
        $request->validate([
            'code' => 'required',
        ]);

        $user     = auth()->user();
        $response = verifyG2fa($user, $request->code);
        if ($response) {
            $user->tsc = null;
            $user->ts  = Status::DISABLE;
            $user->save();
            $notify[] = ['success', 'Two factor authenticator deactivated successfully'];
        } else {
            $notify[] = ['error', 'Wrong verification code'];
        }
        return back()->withNotify($notify);
    }

    public function transactions(Request $request) {
        $pageTitle    = 'Transactions';
        $remarks      = Transaction::whereNotNull('remark')->where('user_id', auth()->id())->distinct('remark')->orderBy('remark')->get('remark');
        $coins        = Transaction::where('user_id', auth()->id())->distinct('currency')->orderBy('currency')->get('currency');
        $transactions = Transaction::where('user_id', auth()->id());

        if ($request->search) {
            $transactions->where('trx', $request->search);
        }

        if ($request->wallet_type) {
            $transactions->where('wallet_type', $request->wallet_type);
        }

        if ($request->type) {
            $transactions->where('trx_type', $request->type);
        }

        if ($request->remark) {
            $transactions->where('remark', $request->remark);
        }

        if ($request->currency_code) {
            $transactions->where('currency', $request->currency_code);
        }

        if ($request->ref == 'total_earning') {
            $request->merge([
                // Total earning refers to mining/investment earning wallet only
                // (referral commissions are separate)
                'wallet_type' => Status::PROFIT_WALLET,
            ]);

            $transactions->where('wallet_type', Status::PROFIT_WALLET);
        }

        $transactions = $transactions->orderBy('id', 'desc')->with('badgeReward.badge')->paginate(getPaginate());
        return view('Template::user.transactions', compact('pageTitle', 'transactions', 'remarks', 'coins'));
    }

    public function kycForm() {
        if (auth()->user()->kv == Status::KYC_PENDING) {
            $notify[] = ['error', 'Your KYC is under review'];
            return to_route('user.home')->withNotify($notify);
        }
        if (auth()->user()->kv == Status::KYC_VERIFIED) {
            $notify[] = ['error', 'You are already KYC verified'];
            return to_route('user.home')->withNotify($notify);
        }
        $pageTitle = 'KYC Form';
        $form      = Form::where('act', 'kyc')->first();
        return view('Template::user.kyc.form', compact('pageTitle', 'form'));
    }

    public function kycData() {
        $user      = auth()->user();
        $pageTitle = 'KYC Data';
        abort_if($user->kv == Status::VERIFIED, 403);
        return view('Template::user.kyc.info', compact('pageTitle', 'user'));
    }

    public function kycSubmit(Request $request) {
        $form           = Form::where('act', 'kyc')->firstOrFail();
        $formData       = $form->form_data;
        $formProcessor  = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);
        $request->validate($validationRule);
        $user = auth()->user();
        foreach (isset($user->kyc_data) ? $user->kyc_data : [] as $kycData) {
            if ($kycData->type == 'file') {
                fileManager()->removeFile(getFilePath('verify') . '/' . $kycData->value);
            }
        }
        $userData                   = $formProcessor->processFormData($request, $formData);
        $user->kyc_data             = $userData;
        $user->kyc_rejection_reason = null;
        $user->kv                   = Status::KYC_PENDING;
        $user->save();

        $notify[] = ['success', 'KYC data submitted successfully'];
        return to_route('user.home')->withNotify($notify);
    }

    public function referral() {
        $general = gs();

        if (!$general->referral_system) {
            $notify[] = ['error', 'Sorry, the referral system is currently unavailable'];
            return back()->withNotify($notify);
        }

        $pageTitle = "My Referrals";
        $maxLevel  = Referral::max('level');
        $relations = [];

        for ($label = 1; $label <= $maxLevel; $label++) {
            $relations[$label] = (isset($relations[$label - 1]) ? $relations[$label - 1] . '.allReferrals' : 'allReferrals');
        }

        $user = auth()->user()->load($relations);
        return view('Template::user.referral.index', compact('pageTitle', 'user', 'maxLevel'));
    }

    public function referralLog() {

        if (!gs()->referral_system) {
            $notify[] = ['error', 'Sorry, the referral system is currently unavailable'];
            return back()->withNotify($notify);
        }

        $pageTitle = "Referral Bonus Logs";
        $logs      = ReferralLog::where('referee_id', auth()->id())->with('referee')->orderBy('id', 'desc')->paginate(getPaginate());
        $totalEarned = ReferralLog::where('referee_id', auth()->id())->sum('amount');

        return view('Template::user.referral.logs', compact('pageTitle', 'logs', 'totalEarned'));
    }

    public function wallets() {
        $pageTitle        = "Miner Wallets";
        $userCoinBalances = UserCoinBalance::where('user_id', auth()->id());

        if (request()->currency_code) {
            $userCoinBalances = $userCoinBalances->whereHas('miner', function ($miner) {
                $miner->where('currency_code', request()->currency_code);
            });
        }

        $userCoinBalances = $userCoinBalances->with('miner')->get();
        return view('Template::user.wallets', compact('pageTitle', 'userCoinBalances'));
    }

    public function transferToProfitWallet(Request $request, $id) {

        $user     = auth()->user();
        $userCoin = UserCoinBalance::where('user_id', $user->id)->findOrFail($id);

        $request->validate([
            "amount" => 'required|numeric|gt:0|max:' . $userCoin->balance,
        ], [
            "amount.max" => "Amount must be less than or equal to " . getAmount($userCoin->balance, 8) . " " . $userCoin->miner->currency_code,
        ]);

        $amount      = (float) $request->amount;
        $symbol      = $userCoin->miner->currency_code;
        $rate        = $userCoin->miner->rate;
        $finalAmount = $amount * $rate;

        $user->profit_wallet += $finalAmount;
        $user->save();

        $userCoin->balance -= $amount;
        $userCoin->save();

        $trx          = getTrx();

        $transaction               = new Transaction();
        $transaction->user_id      = $user->id;
        $transaction->amount       = $amount;
        $transaction->post_balance = $userCoin->balance;
        $transaction->charge       = 0;
        $transaction->wallet_type  = Status::CRYPTO_WALLET;
        $transaction->trx_type     = '-';
        $transaction->details      = 'Transferred to earning wallet';
        $transaction->remark       = 'transferred_to_earning_wallet';
        $transaction->trx          = $trx;
        $transaction->rate         = $rate;
        $transaction->currency     = $symbol;
        $transaction->save();

        $transaction               = new Transaction();
        $transaction->user_id      = $user->id;
        $transaction->amount       = $finalAmount;
        $transaction->post_balance = $user->profit_wallet;
        $transaction->charge       = 0;
        $transaction->wallet_type  = Status::PROFIT_WALLET;
        $transaction->trx_type     = '+';
        $transaction->details      = 'Added from crypto wallet';
        $transaction->remark       = 'added_from_crypto_wallet';
        $transaction->trx          = $trx;
        $transaction->currency     = gs('cur_text');
        $transaction->save();

        $notify[] = ['success', 'Amount transferred successfully to earning wallet'];
        return back()->withNotify($notify);
    }

    public function addDeviceToken(Request $request) {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return ['success' => false, 'errors' => $validator->errors()->all()];
        }

        $deviceToken = DeviceToken::where('token', $request->token)->first();

        if ($deviceToken) {
            return ['success' => true, 'message' => 'Already exists'];
        }

        $deviceToken          = new DeviceToken();
        $deviceToken->user_id = auth()->user()->id;
        $deviceToken->token   = $request->token;
        $deviceToken->is_app  = Status::NO;
        $deviceToken->save();

        return ['success' => true, 'message' => 'Token saved successfully'];
    }

    public function downloadAttachment($fileHash) {
        $filePath  = decrypt($fileHash);
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $title     = slug(gs('site_name')) . '- attachments.' . $extension;
        try {
            $mimetype = mime_content_type($filePath);
        } catch (\Exception $e) {
            $notify[] = ['error', 'File does not exists'];
            return back()->withNotify($notify);
        }
        header('Content-Disposition: attachment; filename="' . $title);
        header("Content-Type: " . $mimetype);
        return readfile($filePath);
    }
}
