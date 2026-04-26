<?php

namespace App\Http\Controllers\Api;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\PaymentController as GatewayPaymentController;
use App\Lib\FormProcessor;
use App\Models\Deposit;
use App\Models\GatewayCurrency;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\AdminNotification;

class PaymentController extends Controller {

    public function methods() {
        $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })->with('method')->orderby('name')->get();

        $notify[] = 'Payment methods';

        return responseSuccess('deposit_methods', $notify, [
            'methods' => $gatewayCurrency,
            'image_path' => getFilePath('gateway')
        ]);
    }

    public function depositInsert(Request $request) {
        $validator = Validator::make($request->all(), [
            'amount'      => 'required|numeric|gt:0',
            'method_code' => 'required',
            'currency'    => 'required',
            'order'       => 'nullable|integer|gte:0'
        ]);

        if ($validator->fails()) {
            return responseError('validation_error', $validator->errors());
        }

        $order        = null;
        $amount       = $request->amount;
        $notification = 'Please follow deposit limit';


        if ($request->has('order') && $request->order > 0) {
            $order = Order::where('id', $request->order)->where('user_id', auth()->id())->unpaid()->first();

            if (!$order) {
                $notify[] = ['error', 'Order not found!'];
                return responseError('validation_error', $notify);
            }
            $amount       = $order->amount;
            $notification = 'Please follow payment limit';
        }

        $user = auth()->user();
        $deposit = new Deposit();
        $deposit->from_api = 1;
        $deposit->is_web = $request->is_web ? 1 : 0;

        $deposit->order_id = $order ? $order->id : 0;

        $gate = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })->where('method_code', $request->method_code)->where('currency', $request->currency)->first();
        if (!$gate) {
            $notify[] = 'Invalid gateway';
            return responseError('invalid_gateway', $notify);
        }

        if ($gate->min_amount > $amount || $gate->max_amount < $amount) {
            return responseError('invalid_amount', $notification);
        }

        $charge                   = $gate->fixed_charge + ($amount * $gate->percent_charge / 100);
        $payable                  = $amount + $charge;
        $finalAmount              = $payable * $gate->rate;

        $deposit->user_id         = $user->id;
        $deposit->method_code     = $gate->method_code;
        $deposit->method_currency = strtoupper($gate->currency);
        $deposit->amount          = $amount;
        $deposit->charge          = $charge;
        $deposit->rate            = $gate->rate;
        $deposit->final_amount    = $finalAmount;
        $deposit->btc_amount      = 0;
        $deposit->btc_wallet      = "";
        $deposit->trx             = getTrx();
        $deposit->success_url     = $order ? route('user.plans.purchased') : route('user.deposit.history');
        $deposit->failed_url      = route('user.deposit.index');
        $deposit->save();

        $notify[] =  'Payment Inserted';

        if ($request->is_web && $deposit->gateway->code < 1000) {
            $dirName = $deposit->gateway->alias;
            $new = 'App\\Http\\Controllers\\Gateway\\' . $dirName . '\\ProcessController';

            $gatewayData = $new::process($deposit);
            $gatewayData = json_decode($gatewayData);

            // for Stripe V3
            if (isset($data->session)) {
                $deposit->btc_wallet = $gatewayData->session->id;
                $deposit->save();
            }

            return responseSuccess('deposit_inserted', $notify, [
                'deposit' => $deposit,
                'gateway_data' => $gatewayData
            ]);
        }

        $deposit->load('gateway', 'gateway.form');

        return responseSuccess('payment_inserted', $notify, [
            'payment' => $deposit,
            'redirect_url' => route('deposit.app.confirm', encrypt($deposit->id))
        ]);
    }

    public function appPaymentConfirm(Request $request) {
        if (!gs('in_app_payment')) {
            $notify[] = 'In app purchase feature currently disable';
            return responseError('feature_disable', $notify);
        }
        $validator = Validator::make($request->all(), [
            'method_code'   => 'required|in:5001',
            'amount'        => 'required|numeric|gt:0',
            'currency'      => 'required|string',
            'purchase_token' => 'required',
            'package_name'   => 'required',
            'plan_id'     => 'required'
        ]);

        if ($validator->fails()) {
            return responseError('validation_error', $validator->errors());
        }

        $user = auth()->user();

        $deposit = Deposit::where('status', Status::PAYMENT_SUCCESS)->where('btc_wallet', $request->purchase_token)->exists();
        if ($deposit) {
            $notify[] =  'Payment already captured';
            return responseError('payment_captured', $notify);
        }


        if (!file_exists(getFilePath('appPurchase') . '/google_pay.json')) {
            $notify[] =  'Configuration file missing';
            return responseError('configuration_missing', $notify);
        }
        $configuration = getFilePath('appPurchase') . '/google_pay.json';
        $client          = new \Google_Client();
        $client->setAuthConfig($configuration);
        $client->setScopes([\Google_Service_AndroidPublisher::ANDROIDPUBLISHER]);
        $service = new \Google_Service_AndroidPublisher($client);

        $packageName   = $request->package_name;
        $productId     = $request->plan_id;
        $purchaseToken = $request->purchase_token;
        try {
            $response = $service->purchases_products->get($packageName, $productId, $purchaseToken);
        } catch (\Exception $e) {
            $errorJson = json_decode($e->getMessage());
            $errorMessage = isset($errorJson->error->message) && $errorJson->error->message ? $errorJson->error->message : '';
            $adminNotification = new AdminNotification();
            $adminNotification->user_id = $user->id;
            $adminNotification->title = 'In App Purchase Error: ' . $errorMessage;
            $adminNotification->click_url = '#';
            $adminNotification->save();


            $notify[] = 'Something went wrong';
            return responseError('invalid_purchase', $notify);
        }

        if ($response->getPurchaseState() != 0) {
            $notify[] = 'Invalid purchase';
            return responseError('invalid_purchase', $notify);
        }

        //the amount should be your product amount
        $amount = 10;
        $rate = $request->amount / $amount;


        $data = new Deposit();
        $data->user_id = $user->id;
        $data->method_code = $request->method_code;
        $data->method_currency = $request->currency;
        $data->amount = $amount;
        $data->charge = 0;
        $data->rate = $rate;
        $data->final_amount = $request->amount;
        $data->btc_amount = 0;
        $data->btc_wallet = $request->purchase_token;
        $data->trx = getTrx();
        $data->save();

        GatewayPaymentController::userDataUpdate($data);

        $notify[] = 'Payment confirmed successfully';
        return responseSuccess('payment_confirm', $notify);
    }
    public function manualDepositConfirm(Request $request) {
        $track = $request->track;
        $data = Deposit::with('gateway')->where('status', Status::PAYMENT_INITIATE)->where('trx', $track)->first();

        if (!$data) {
            $notify[] = 'Invalid request';
            return responseError('invalid_request', $notify);
        }

        $gatewayCurrency = $data->gatewayCurrency();
        $gateway = $gatewayCurrency->method;
        $formData = $gateway->form->form_data;

        $formProcessor = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);
        $request->validate($validationRule);
        $userData = $formProcessor->processFormData($request, $formData);

        $data->detail = $userData;
        $data->status = Status::PAYMENT_PENDING;
        $data->save();

        $adminNotification            = new AdminNotification();
        $adminNotification->user_id   = $data->user->id;
        $adminNotification->title     = 'Deposit request from ' . $data->user->username;
        $adminNotification->click_url = urlPath('admin.deposit.details', $data->id);
        $adminNotification->save();

        notify($data->user, 'DEPOSIT_REQUEST', [
            'method_name'     => $data->gatewayCurrency()->name,
            'method_currency' => $data->method_currency,
            'method_amount'   => showAmount($data->final_amount, currencyFormat: false),
            'amount'          => showAmount($data->amount, currencyFormat: false),
            'charge'          => showAmount($data->charge, currencyFormat: false),
            'rate'            => showAmount($data->rate, currencyFormat: false),
            'trx'             => $data->trx
        ]);

        $notify[] = ['You have deposit request has been taken'];
        return responseSuccess('deposit_request_taken', $notify);
    }
}
