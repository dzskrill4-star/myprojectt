<?php

namespace App\Http\Controllers;

use App\Constants\Status;
use App\Lib\CurlRequest;
use App\Models\AdminNotification;
use App\Models\Badge;
use App\Models\BadgeReward;
use App\Models\CronJob;
use App\Models\CronJobLog;
use App\Models\Miner;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\UserBadge;
use App\Models\UserCoinBalance;
use Carbon\Carbon;

class CronController extends Controller {

    public function cron() {
        $general            = gs();
        $general->last_cron = now();
        $general->save();

        $crons = CronJob::with('schedule');

        if (request()->alias) {
            $crons->where('alias', request()->alias);
        } else {
            $crons->where('next_run', '<', now())->where('is_running', Status::YES);
        }

        $crons = $crons->get();

        foreach ($crons as $cron) {
            $cronLog              = new CronJobLog();
            $cronLog->cron_job_id = $cron->id;
            $cronLog->start_at    = now();
            if ($cron->is_default) {
                $controller = new $cron->action[0];
                try {
                    $method = $cron->action[1];
                    $controller->$method();
                } catch (\Exception $e) {
                    $cronLog->error = $e->getMessage();
                }
            } else {
                try {
                    CurlRequest::curlContent($cron->url);
                } catch (\Exception $e) {
                    $cronLog->error = $e->getMessage();
                }
            }
            $cron->last_run = now();
$interval = (int) $cron->schedule->interval;
$cron->next_run = now()->addSeconds($interval);
            $cron->save();

            $cronLog->end_at = $cron->last_run;

            $startTime         = Carbon::parse($cronLog->start_at);
            $endTime           = Carbon::parse($cronLog->end_at);
            $diffInSeconds     = $startTime->diffInSeconds($endTime);
            $cronLog->duration = $diffInSeconds;
            $cronLog->save();
        }

        if (request()->target == 'all') {
            $notify[] = ['success', 'Cron executed successfully'];
            return back()->withNotify($notify);
        }

        if (request()->alias) {
            $notify[] = ['success', keyToTitle(request()->alias) . ' executed successfully'];
            return back()->withNotify($notify);
        }
    }

    public function returnAmount() {
        $general            = gs();
        $general->last_cron = Carbon::now()->toDateTimeString();

        $general->save();
        $orders = Order::approved()
            ->with('user', 'miner')
            ->whereHas('user')
            ->where('period_remain', '>=', 1)
            ->where('last_paid', '<=', Carbon::now()->subHours(24)->toDateTimeString())
            ->get();

        foreach ($orders as $order) {
            $returnAmount    = rand($order->min_return_per_day * 100000000, $order->max_return_per_day * 100000000) / 100000000;
            $userCoinBalance = UserCoinBalance::where('user_id', $order->user_id)->where('miner_id', $order->miner_id)->first();

            if (!$userCoinBalance) {
                continue;
            }

            $user               = $order->user;
            $userBadge          = $user->badge;
            $boostEarningAmount = 0;

            if ($userBadge) {
                $boostEarningPercent = $userBadge->badge->earning_boost;
                $boostEarningAmount  = $returnAmount * $boostEarningPercent / 100;
                $returnAmount += $boostEarningAmount;
            }

            $userCoinBalance->balance += $returnAmount;
            $userCoinBalance->save();

$user->profit_wallet += $returnAmount;
$user->save();

            $order->period_remain -= 1;
            $order->last_paid = Carbon::now();
            $order->save();

            $trx = getTrx();

            $transaction               = new Transaction();
            $transaction->user_id      = $order->user_id;
            $transaction->order_id     = $order->id;
            $transaction->amount       = $returnAmount;
            $transaction->post_balance = getAmount($userCoinBalance->balance, 16);
            $transaction->charge       = 0;
$transaction->wallet_type = Status::PROFIT_WALLET;
            $transaction->trx_type     = '+';
            $transaction->details      = 'Daily return amount received';
            $transaction->trx          = $trx;
            $transaction->currency     = $order->miner->currency_code;
            $transaction->remark       = 'return_amount';
            $transaction->save();

            if ($boostEarningAmount > 0) {
                $badgeReward                 = new BadgeReward();
                $badgeReward->user_id        = $order->user_id;
                $badgeReward->user_badge_id  = $userBadge->id;
                $badgeReward->badge_id       = $userBadge->badge_id;
                $badgeReward->transaction_id = $transaction->id;
                $badgeReward->amount         = $boostEarningAmount;
                $badgeReward->currency       = $transaction->currency;
                $badgeReward->remark         = 'earning_boost';
                $badgeReward->save();
            }

            $this->setUserBadge($user);

            $maintenanceCost = $returnAmount * $order->maintenance_cost / 100;

            $badgeDiscountAmount = 0;

            if ($maintenanceCost && $userBadge) {
                $badgeDiscountAmount = $maintenanceCost * $userBadge->badge->discount_maintenance_cost / 100;
                $maintenanceCost -= $badgeDiscountAmount;
            }

            if ($maintenanceCost > 0) {
                $userCoinBalance->balance -= $maintenanceCost;
                $userCoinBalance->save();

                $transaction               = new Transaction();
                $transaction->order_id     = $order->id;
                $transaction->user_id      = $order->user_id;
                $transaction->amount       = $maintenanceCost;
                $transaction->post_balance = getAmount($userCoinBalance->balance, 16);
                $transaction->charge       = 0;
$transaction->wallet_type = Status::PROFIT_WALLET;
                $transaction->trx_type     = '-';
                $transaction->details      = 'Deducted as maintenance charge';
                $transaction->trx          = $trx;
                $transaction->currency     = $order->miner->currency_code;
                $transaction->remark       = 'maintenance_cost';
                $transaction->save();

                if ($badgeDiscountAmount > 0) {
                    $badgeReward                 = new BadgeReward();
                    $badgeReward->user_id        = $order->user_id;
                    $badgeReward->user_badge_id  = $userBadge->id;
                    $badgeReward->badge_id       = $userBadge->badge_id;
                    $badgeReward->transaction_id = $transaction->id;
                    $badgeReward->amount         = $badgeDiscountAmount;
                    $badgeReward->currency       = $transaction->currency;
                    $badgeReward->remark         = 'discount_maintenance_cost';
                    $badgeReward->save();
                }
            }
        }
    }

    private function setUserBadge($user) {
        $earnedAmount = $user->totalEarningAmount();
        $userBadges   = $user->badges()->pluck('badge_id')->toArray();
        $badges       = Badge::where('earning_amount', '<=', $earnedAmount)->whereNotIn('id', $userBadges)->orderBy('earning_amount')->get();

        if ($badges->isEmpty()) {
            return;
        }

        $sequenceNumber = UserBadge::where('user_id', $user->id)->max('sequence_number') + 1;

        foreach ($badges as $badge) {
            $userBadge                  = new UserBadge();
            $userBadge->user_id         = $user->id;
            $userBadge->badge_id        = $badge->id;
            $userBadge->sequence_number = $sequenceNumber;
            $userBadge->unlocked_at     = now();
            $userBadge->save();

            notify($user, 'USER_BADGE_UPGRADE', [
                'username'       => $user->fullname,
                'name'           => $badge->name,
                'earning_amount' => showAmount($badge->earning_amount, currencyFormat: false),
            ]);

            $sequenceNumber += 1;
        }
    }

    public function cryptoRate() {
        $general         = gs();
        $defaultCurrency = $general->cur_text;
        $url             = 'https://pro-api.coinmarketcap.com/v2/cryptocurrency/quotes/latest';
        $cryptos         = Miner::pluck('currency_code')->toArray();
        $cryptos         = implode(',', $cryptos);

        $parameters = [
            'symbol'  => $cryptos,
            'convert' => $defaultCurrency,
        ];

        $headers = [
            'Accepts: application/json',
            'X-CMC_PRO_API_KEY:' . trim($general->crypto_currency_api),
        ];

        $qs      = http_build_query($parameters);
        $request = "{$url}?{$qs}";
        $curl    = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL            => $request,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_RETURNTRANSFER => 1,
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response);

        if (isset($response->status->error_message) && $response->status->error_message) {
            $adminNotification            = new AdminNotification();
            $adminNotification->user_id   = 0;
            $adminNotification->title     = $response->status->error_message;
            $adminNotification->click_url = '#';
            $adminNotification->save();
        }

        $coins = $response->data ?? [];

        foreach ($coins as $key => $coin) {
            $currency = Miner::where('currency_code', $key)->first();

            if ($currency) {
                $currency->rate = $coin[0]->quote->$defaultCurrency->price;
                $currency->save();
            }
        }
    }
}
