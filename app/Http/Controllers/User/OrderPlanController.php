<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\BadgeReward;
use App\Models\Order;
use App\Models\Plan;
use App\Models\Transaction;
use App\Models\UserCoinBalance;
use Illuminate\Http\Request;

class OrderPlanController extends Controller {

    public function orderPlan(Request $request) {
        $request->validate([
            'plan_id'        => 'required|exists:plans,id',
            'payment_method' => 'required|integer|in:1,2,3',
        ], [
            'payment_method.required' => 'Please select a payment option',
        ]);

        $plan = Plan::active()->whereHas('miner')->with('miner')->findOrFail($request->plan_id);

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
                $notify[] = ['error', __('manual.payment_via_profit_wallet_not_allowed')];
                return back()->withNotify($notify);
            }

            if ($user->profit_wallet < $planPrice) {
                $notify[] = ['error', 'Insufficient earning wallet balance'];
                return back()->withNotify($notify);
            }
        }

        if ($request->payment_method == Status::BALANCE && $user->balance < $planPrice) {
            $notify[] = ['error', 'Insufficient deposit wallet balance'];
            return back()->withNotify($notify);
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

        $period                    = totalPeriodInDay($plan->period, $plan->period_unit);

        $order                     = new Order();
        $order->trx                = getTrx();
        $order->user_id            = $user->id;
        $order->plan_id            = $plan->id;
        $order->plan_details       = $planDetails;
        $order->amount             = $planPrice;
        $order->min_return_per_day = $plan->min_return_per_day;
        $order->max_return_per_day = $plan->max_return_per_day ?? $plan->min_return_per_day;
        $order->miner_id           = $plan->miner->id;
        $order->maintenance_cost   = $plan->maintenance_cost;
        $order->period             = $period;
        $order->period_remain      = $period;

        if ($request->payment_method == Status::BALANCE) {
            return self::makeOrderByBalanceProfit($order, $user, Status::BALANCE, $plan->title);
        } else if ($request->payment_method == Status::PROFIT) {
            return self::makeOrderByBalanceProfit($order, $user, Status::PROFIT, $plan->title);
        } else {
            $order->status = Status::ORDER_UNPAID;
            $order->save();
            return redirect()->route('user.payment', encrypt($order->id));
        }
    }

    private static function makeOrderByBalanceProfit($order, $user, $walletType, $planTitle) {
        $order->status = Status::ORDER_APPROVED;
        $order->save();

        //Check If Exists
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
            $badgeReward                = new BadgeReward();
            $badgeReward->user_id       = $order->user_id;
            $badgeReward->user_badge_id = $order->plan_details->user_badge_id;
            $badgeReward->badge_id      = $order->plan_details->badge_id;
            $badgeReward->transaction_id = $transaction->id;
            $badgeReward->amount        = $order->plan_details->badge_discount;
            $badgeReward->currency      = $transaction->currency;
            $badgeReward->remark        = 'plan_price_discount';
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

        $notify[] = ['success', 'Plan purchased successfully'];
        return to_route('user.plans.purchased')->withNotify($notify);
    }

    public function miningTracks() {
        $pageTitle = "Mining Tracks";
        $orders = Order::where('orders.user_id', auth()->id())
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
            ->paginate(getPaginate());

        return view('Template::user.mining_tracks', compact('pageTitle', 'orders'));
    }
}
