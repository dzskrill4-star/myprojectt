<?php

namespace App\Http\Controllers\Api;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\BadgeReward;
use App\Models\Miner;
use App\Models\Order;
use App\Models\Plan;
use App\Models\Transaction;
use App\Models\UserBadge;
use App\Models\UserCoinBalance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderPlanController extends Controller {
    public function plans() {
        $miners   = Miner::with('activePlans')->whereHas('activePlans')->get();
        $notify[] = 'Active plans';
        $user = auth()->user();

        return responseSuccess('active_plans', $notify, [
            'miners' => $miners,
            'current_balance' => $user->balance,
            'profit_wallet_balance' => $user->profit_wallet,
        ]);
    }

    public function orderPlan(Request $request) {
        $validator = Validator::make($request->all(), [
            'plan_id'        => 'required',
            'payment_method' => 'required|integer|in:1,2,3',
        ], [
            'payment_method.required' => 'Please Select a Payment System',
        ]);

        if ($validator->fails()) {
            return responseError('validation_error', $validator->errors());
        }

        $plan = Plan::where('id', $request->plan_id)->active()->with('miner')->first();

        if (!$plan) {
            $notify[] = 'Plan doesn\'t exist';
            return responseError('validation_error', $notify);
        }

        $planPrice = $plan->price;

        $user = auth()->user();

        $userBadge = $user->badge;
        $discountForBadge = 0;

        if ($userBadge) {
            $discountForBadge = $planPrice * $userBadge->badge->plan_price_discount / 100;
            $planPrice = $planPrice - $discountForBadge;
        }

        if ($request->payment_method == Status::PROFIT) {
            // التحقق من السماح بالدفع عبر محفظة الأرباح
            if ($user->allow_profit_wallet_payment == 0) {
                $notify[] = __('manual.payment_via_profit_wallet_not_allowed');
                return responseError('validation_error', $notify);
            }

            if ($user->profit_wallet < $planPrice) {
                $notify[] = 'Insufficient earning wallet balance';
                return responseError('validation_error', $notify);
            }
        }

        if ($request->payment_method == Status::BALANCE && $user->balance < $planPrice) {
            $notify[] = 'Insufficient deposit wallet balance';
            return responseError('validation_error', $notify);
        }

        $planDetails = [
            'title'          => $plan->title,
            'miner'          => $plan->miner->name,
            'speed'          => $plan->speed . ' ' . $plan->speedUnitText,
            'period'         => $plan->period . ' ' . $plan->periodUnitText,
            'period_value'   => $plan->period,
            'period_unit'    => $plan->period_unit,
            'user_badge_id'  => $userBadge ? $userBadge->id : 0,
            'badge_id'       => $userBadge ? $userBadge->badge_id : 0,
            'badge_discount' => $discountForBadge,
        ];

        $order                     = new Order();
        $order->trx                = getTrx();
        $order->user_id            = $user->id;
        $order->plan_id            = $plan->id;
        $order->plan_details       = $planDetails;
        $order->amount             = $planPrice;
        $order->min_return_per_day = $plan->min_return_per_day;
        $order->max_return_per_day = $plan->max_return_per_day ?? $plan->min_return_per_day;
        $order->miner_id           = $plan->miner_id;
        $order->maintenance_cost   = $plan->maintenance_cost;
        $period                    = totalPeriodInDay($plan->period, $plan->period_unit);
        $order->period             = $period;
        $order->period_remain      = $period;

        if ($request->payment_method == Status::BALANCE) {
            return self::makeApiOrderByBalanceProfit($order, $user, Status::BALANCE, $plan->title);
        } else if ($request->payment_method == Status::PROFIT) {
            return self::makeApiOrderByBalanceProfit($order, $user, Status::PROFIT, $plan->title);
        } else {
            $order->status = Status::ORDER_UNPAID;
            $order->save();

            $notify[]     = 'Payment methods';
            $orderDetails = [
                'order_id'   => $order->id,
                'plan_title' => $plan->title,
                'amount'     => $order->amount,
            ];

            return responseSuccess('payment_methods', $notify, [
                'order' => $orderDetails,
                'redirect_url' => route('api.payment.method')
            ]);
        }
    }

    private static function makeApiOrderByBalanceProfit($order, $user, $walletType, $planTitle)
    {
        $order->status = Status::ORDER_APPROVED;
        $order->save();

        UserCoinBalance::where('user_id', $user->id)->where('miner_id', $order->miner_id)->firstOrCreate([
            'user_id'  => $user->id,
            'miner_id' => $order->miner_id,
        ]);

        if ($walletType == Status::BALANCE) {
            $user->balance -= $order->amount;
        } else {
            $user->profit_wallet -= $order->amount;
        }

        $user->save();

        $referrer = $user->referrer;

        if (gs('referral_system') && $referrer) {
            levelCommission($user, $order->amount, $order->trx);
        }

        $transaction               = new Transaction();
        $transaction->user_id      = $order->user_id;
        $transaction->amount       = getAmount($order->amount);
        $transaction->charge       = 0;
        $transaction->currency     = gs('cur_text');
        $transaction->post_balance = $walletType == Status::BALANCE ? $user->balance : $user->profit_wallet;
        $transaction->wallet_type  = ($walletType == Status::BALANCE) ? Status::DEPOSIT_WALLET : Status::PROFIT_WALLET;
        $transaction->trx_type     = '-';
        $transaction->details      = 'New mining plan purchased';
        $transaction->remark       = 'payment';
        $transaction->trx          = $order->trx;
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

        notify($user, 'PAYMENT_VIA_USER_BALANCE', [
            'plan_title'      => $planTitle,
            'wallet_type'     => ($walletType == Status::BALANCE) ? "Deposit Wallet" : "Earning Wallet",
            'amount'          => showAmount($order->amount, currencyFormat: false),
            'method_currency' => gs('cur_text'),
            'post_balance'    => showAmount($user->balance, currencyFormat: false),
            'method_name'     => gs('cur_text') . ' Balance',
            'order_id'        => $order->trx,
        ]);

        $notify[] = 'Plan purchased successfully';

        return responseSuccess('plan_purchased', $notify);
    }

}
