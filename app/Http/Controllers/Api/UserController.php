<?php

namespace App\Http\Controllers\Api;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Lib\GoogleAuthenticator;
use App\Models\DeviceToken;
use App\Models\Form;
use App\Models\NotificationLog;
use App\Models\Order;
use App\Models\Referral;
use App\Models\ReferralLog;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserCoinBalance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller {
    public function dashboard() {

        $user = auth()->user();
        $referralLink = route('home') . "?reference=" . $user->username;

        $widget['deposit_wallet']            = showAmount($user->balance, currencyFormat: false);
        $widget['profit_wallet']             = showAmount($user->profit_wallet, currencyFormat: false);
        $widget['total_returned_amount']     =  $user->totalReturnedAmount();
        $widget['total_referral_commission'] = $user->totalReferralCommission();
        // Total earning is mining/investment only (referral commissions are separate)
        $widget['total_earning']             = $widget['total_returned_amount'];

        $transactions = $transactions = Transaction::where('user_id', $user->id)->orderBy('id', 'desc')->limit(10)->get();

        $notify[] = 'All dashboard data';
        return responseSuccess('dashboard_data', $notify, [
            'user'            => $user,
            'referral_link'   => $referralLink,
            'widget'          => $widget,
            'transactions'    => $transactions,
        ]);
    }

    public function kycForm() {
        if (auth()->user()->kv == Status::KYC_PENDING) {
            $notify[] = 'Your KYC is under review';
            return responseSuccess('under_review', $notify, [
                'kyc_data'   => auth()->user()->kyc_data,
                'image_path' => getFilePath('verify'),
            ]);
        }
        if (auth()->user()->kv == Status::KYC_VERIFIED) {
            $notify[] = 'You are already KYC verified';
            return responseError('already_verified', $notify);
        }
        $form     = Form::where('act', 'kyc')->first();
        $notify[] = 'KYC field is below';
        return responseSuccess('kyc_form', $notify, ['form' => $form->form_data]);
    }

    public function kycSubmit(Request $request) {
        $form = Form::where('act', 'kyc')->first();
        if (!$form) {
            $notify[] = 'Invalid KYC request';
            return responseError('invalid_request', $notify);
        }
        $formData       = $form->form_data;
        $formProcessor  = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);

        $validator = Validator::make($request->all(), $validationRule);

        if ($validator->fails()) {
            return responseError('validation_error', $validator->errors());
        }

        $user = auth()->user();

        foreach (isset($user->kyc_data) ? $user->kyc_data : [] as $kycData) {
            if ($kycData->type == 'file') {
                fileManager()->removeFile(getFilePath('verify') . '/' . $kycData->value);
            }
        }

        $userData = $formProcessor->processFormData($request, $formData);

        $user->kyc_data             = $userData;
        $user->kyc_rejection_reason = null;
        $user->kv                   = Status::KYC_PENDING;
        $user->save();

        $notify[] = 'KYC data submitted successfully';
        return responseSuccess('kyc_submitted', $notify, ['kyc_data' => $user->kyc_data]);
    }
    public function kycData() {
        $user      = auth()->user();
        $kycData   = $user->kyc_data ?? [];
        $kycValues = [];
        foreach ($kycData as $kycInfo) {
            if (!$kycInfo->value) {
                continue;
            }
            if ($kycInfo->type == 'checkbox') {
                $value = implode(', ', $kycInfo->value);
            } else if ($kycInfo->type == 'file') {
                $value = encrypt(getFilePath('verify') . '/' . $kycInfo->value);
            } else {
                $value = $kycInfo->value;
            }

            $kycValues[] = [
                'name'  => $kycInfo->name,
                'type'  => $kycInfo->type,
                'value' => $value,
            ];
        }
        $notify[] = 'KYC data';
        return responseSuccess('kyc_data', $notify, ['kyc_data' => $kycValues]);
    }

    public function wallets() {
        $user = User::where('id', auth()->id())->with('coinBalances:id,user_id,miner_id,balance', 'coinBalances.miner')->first();
        if (!$user) {
            $notify[] = 'User doesn\'t exist!';
            return responseError('validation_error', $notify);
        }

        $notify[] = 'User coin wallets';
        return responseSuccess('wallets', $notify, ['coin_balances' => $user->coinBalances]);
    }

    public function transferToProfitWallet(Request $request, $id) {
        $user     = auth()->user();
        $userCoin = UserCoinBalance::where('user_id', $user->id)->find($id);

        if (!$userCoin) {
            $notify[] = 'Mining wallet not found';
            return responseError('validation_error', $notify);
        }

        $validator = Validator::make($request->all(), [
            "amount" => 'required|numeric|min:0.000001',
        ]);
        if ($validator->fails()) {
            return responseError('validation_error', $validator->errors());
        }

        $amount = (float) $request->amount;
        $symbol = $userCoin->miner->currency_code;

        $rate        = $userCoin->miner->rate;
        $finalAmount = $amount * $rate;

        if ($userCoin->balance < $amount) {
            $notify[] = 'Insufficient balance in mining wallet';
            return responseError('validation_error', $notify);
        }

        $user->profit_wallet += $finalAmount;
        $user->save();

        $userCoin->balance -= $amount;
        $userCoin->save();

        $trx = getTrx();

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
        return responseSuccess('transferred_crypto_wallet_balance', $notify);
    }

    public function referral() {
        $general = gs();

        if (!$general->referral_system) {
            $notify[] = 'Sorry, the referral system is currently unavailable';
            return responseError('validation_error', $notify);
        }

        $maxLevel = Referral::max('level');

        $relations = [];
        for ($label = 1; $label <= $maxLevel; $label++) {
            $relations[$label] = (isset($relations[$label - 1]) ? $relations[$label - 1] . '.allReferrals' : 'allReferrals');
        }
        $user = auth()->user()->load($relations);

        $referrals = getReferees($user, $maxLevel);

        $notify[] = 'Referral';
        return responseSuccess('referrals', $notify, [
            'referral_link' => route('home') . '?reference=' . auth()->user()->username,
            'maxLevel'      => $maxLevel,
            'referrals'     => $referrals,
        ]);
    }

    public function referralLog() {

        if (!gs('referral_system')) {
            $notify[] = 'Sorry, the referral system is currently unavailable';
            return responseError('validation_error', $notify);
        }

        $logs = ReferralLog::where('referee_id', auth()->id())->with('referee')->orderBy('id', 'desc')->paginate(getPaginate());

        $notify[] = 'Referral Logs';
        return responseSuccess('referral_logs', $notify, [
            'logs' => $logs,
        ]);
    }

    public function depositHistory(Request $request) {
        $deposits = auth()->user()->deposits()->where('order_id', 0);
        if ($request->search) {
            $deposits = $deposits->where('trx', $request->search);
        }
        $deposits = $deposits->with(['gateway'])->orderBy('id', 'desc')->paginate(getPaginate());
        $notify[] = 'Deposit data';
        return responseSuccess('deposits', $notify, ['deposits' => $deposits]);
    }

    public function paymentHistory(Request $request) {
        $payments = auth()->user()->deposits()->where('order_id', '>', 0);
        if ($request->search) {
            $payments = $payments->where('trx', $request->search);
        }
        $payments = $payments->with(['gateway'])->orderBy('id', 'desc')->paginate(getPaginate());
        $notify[] = 'Payment data';

        return responseSuccess('payments', $notify, ['payments' => $payments]);
    }

    public function transactions(Request $request) {
        $currencies        = Transaction::where('user_id', auth()->id())->distinct('currency')->orderBy('currency')->get('currency');
        $remarks      = Transaction::distinct('remark')->get('remark');
        $transactions = Transaction::where('user_id', auth()->id());

        if ($request->search) {
            $transactions = $transactions->where('trx', $request->search);
        }

        if ($request->type) {
            $type         = $request->type == 'plus' ? '+' : '-';
            $transactions = $transactions->where('trx_type', $type);
        }

        if ($request->remark) {
            $transactions = $transactions->where('remark', $request->remark);
        }

        if ($request->currency_code) {
            $transactions->where('currency', $request->currency_code);
        }

        if ($request->wallet_type) {
            $transactions->where('wallet_type', $request->wallet_type);
        }

        $transactions = $transactions->orderBy('id', 'desc')->paginate(getPaginate());
        $notify[]     = 'Transactions data';
        return responseSuccess('transactions', $notify, [
            'transactions' => $transactions,
            'remarks'      => $remarks,
            'currencies' => $currencies
        ]);
    }

    public function submitProfile(Request $request) {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required',
            'lastname'  => 'required',
        ], [
            'firstname.required' => 'The first name field is required',
            'lastname.required'  => 'The last name field is required',
        ]);

        if ($validator->fails()) {
            return responseError('validation_error', $validator->errors());
        }

        $user = auth()->user();

        $user->firstname = $request->firstname;
        $user->lastname  = $request->lastname;

        $user->address = $request->address;
        $user->zip     = $request->zip;

        $user->save();

        $notify[] = 'Profile updated successfully';
        return responseSuccess('profile_updated', $notify);
    }

    public function submitPassword(Request $request) {
        $passwordValidation = Password::min(6);
        if (gs('secure_password')) {
            $passwordValidation = $passwordValidation->mixedCase()->numbers()->symbols()->uncompromised();
        }

        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password'         => ['required', 'confirmed', $passwordValidation],
        ]);

        if ($validator->fails()) {
            return responseError('validation_error', $validator->errors());
        }

        $user = auth()->user();
        if (Hash::check($request->current_password, $user->password)) {
            $password       = Hash::make($request->password);
            $user->password = $password;
            $user->save();
            $notify[] = 'Password changed successfully';
            return responseSuccess('password_changed', $notify);
        } else {
            $notify[] = 'The password doesn\'t match!';
            return responseError('validation_error', $notify);
        }
    }
    public function addDeviceToken(Request $request) {

        $validator = Validator::make($request->all(), [
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return responseError('validation_error', $validator->errors());
        }

        $deviceToken = DeviceToken::where('token', $request->token)->first();

        if ($deviceToken) {
            $notify[] = 'Token already exists';
            return responseError('token_exists', $notify);
        }

        $deviceToken          = new DeviceToken();
        $deviceToken->user_id = auth()->user()->id;
        $deviceToken->token   = $request->token;
        $deviceToken->is_app  = Status::YES;
        $deviceToken->save();

        $notify[] = 'Token saved successfully';
        return responseSuccess('token_saved', $notify);
    }

    public function show2faForm() {
        $ga        = new GoogleAuthenticator();
        $user      = auth()->user();
        $secret    = $ga->createSecret();
        $qrCodeUrl = $ga->getQRCodeGoogleUrl($user->username . '@' . gs('site_name'), $secret);
        $notify[]  = '2FA Qr';
        return responseSuccess('2fa_qr', $notify, [
            'secret'      => $secret,
            'qr_code_url' => $qrCodeUrl,
        ]);
    }

    public function create2fa(Request $request) {
        $validator = Validator::make($request->all(), [
            'secret' => 'required',
            'code'   => 'required',
        ]);

        if ($validator->fails()) {
            return responseError('validation_error', $validator->errors());
        }

        $user     = auth()->user();
        $response = verifyG2fa($user, $request->code, $request->secret);
        if ($response) {
            $user->tsc = $request->secret;
            $user->ts  = Status::ENABLE;
            $user->save();

            $notify[] = 'Google authenticator activated successfully';
            return responseSuccess('2fa_qr', $notify);
        } else {
            $notify[] = 'Wrong verification code';
            return responseError('wrong_verification', $notify);
        }
    }

    public function disable2fa(Request $request) {
        $validator = Validator::make($request->all(), [
            'code' => 'required',
        ]);

        if ($validator->fails()) {
            return responseError('validation_error', $validator->errors());
        }

        $user     = auth()->user();
        $response = verifyG2fa($user, $request->code);
        if ($response) {
            $user->tsc = null;
            $user->ts  = Status::DISABLE;
            $user->save();
            $notify[] = 'Two factor authenticator deactivated successfully';
            return responseSuccess('2fa_qr', $notify);
        } else {
            $notify[] = 'Wrong verification code';
            return responseError('wrong_verification', $notify);
        }
    }

    public function pushNotifications() {
        $notifications = NotificationLog::where('user_id', auth()->id())->where('sender', 'firebase')->orderBy('id', 'desc')->paginate(getPaginate());
        $notify[]      = 'Push notifications';
        return responseSuccess('notifications', $notify, [
            'notifications' => $notifications,
        ]);
    }

    public function pushNotificationsRead($id) {
        $notification = NotificationLog::where('user_id', auth()->id())->where('sender', 'firebase')->find($id);
        if (!$notification) {
            $notify[] = 'Notification not found';
            return responseError('notification_not_found', $notify);
        }
        $notify[]                = 'Notification marked as read successfully';
        $notification->user_read = 1;
        $notification->save();

        return responseSuccess('notification_read', $notify);
    }

    public function userInfo() {
        $notify[] = 'User information';
        return responseSuccess('user_info', $notify, ['user' => auth()->user()]);
    }

    public function deleteAccount() {
        $user              = auth()->user();
        $user->username    = 'deleted_' . $user->username;
        $user->email       = 'deleted_' . $user->email;
        $user->provider_id = 'deleted_' . $user->provider_id;
        $user->save();

        $user->tokens()->delete();

        $notify[] = 'Account deleted successfully';
        return responseSuccess('account_deleted', $notify);
    }

    public function downloadAttachment($fileHash) {

        try {
            $filePath = decrypt($fileHash);
        } catch (\Exception $e) {
            $notify[] = 'Invalid file';
            return responseError('invalid_failed', $notify);
        }
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $title     = slug(gs('site_name')) . '-attachments.' . $extension;
        try {
            $mimetype = mime_content_type($filePath);
        } catch (\Exception $e) {
            $notify[] = 'File downloaded failed';
            return responseError('download_failed', $notify);
        }
        if (!headers_sent()) {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET,');
            header('Access-Control-Allow-Headers: Content-Type');
        }
        header('Content-Disposition: attachment; filename="' . $title);
        header("Content-Type: " . $mimetype);
        return readfile($filePath);
    }

    public function miningTrack() {
        $miningTracks = Order::where('orders.user_id', auth()->id())
            ->leftJoin('transactions', 'transactions.order_id', '=', 'orders.id')
            ->leftJoin('miners', 'orders.miner_id', '=', 'miners.id')
            ->running()
            ->orderByDesc('orders.id')
            ->selectRaw('orders.*, miners.currency_code, miners.name AS currency_name')
            ->selectRaw("
            SUM(
                CASE
                    WHEN transactions.remark = 'return_amount' THEN transactions.amount
                    WHEN transactions.remark = 'maintenance_cost' THEN -1 * transactions.amount
                    ELSE 0
                END
            ) as total_earned_amount
        ")
            ->groupBy('orders.id')
            ->paginate();

        $notify[] = 'User\'s Mining Tracks';
        return responseSuccess('mining_track', $notify, [
            'mining_tracks' => $miningTracks,
        ]);
    }

    public function orders() {
        $orders    = Order::where('user_id', auth()->id())->where('status', '!=', Status::ORDER_UNPAID)->with('miner')->orderBy('id', 'desc')->paginate();

        $notify[] = 'User\'s Orders';
        return responseSuccess('orders', $notify, [
            'orders' => $orders,
        ]);
    }
}
