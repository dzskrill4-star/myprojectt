<?php

namespace App\Console\Commands;

use App\Constants\Status;
use App\Models\Badge;
use App\Models\BadgeReward;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserBadge;
use App\Models\UserCoinBalance;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessPlans extends Command {
    protected $signature = 'plans:process';

    protected $description = 'Process active plans, accrue earnings, and expire finished plans.';

    public function handle(): int {
        $logger = Log::build([
            'driver' => 'single',
            'path'   => storage_path('logs/plans-cron.log'),
        ]);

        $startedAt = Carbon::now();
        $logger->info('plans:process started', ['time' => $startedAt->toDateTimeString()]);

        $general            = gs();
        $general->last_cron = $startedAt;
        $general->save();

        $cutoff = Carbon::now()->subHours(24);

        $summary = [
            'processed'                => 0,
            'expired'                  => 0,
            'skipped_missing_balance'  => 0,
            'errors'                   => 0,
        ];

        Order::approved()
            ->with(['user.badge.badge', 'miner'])
            ->whereHas('user')
            ->where('period_remain', '>=', 1)
            ->where('last_paid', '<=', $cutoff->toDateTimeString())
            ->chunkById(100, function ($orders) use (&$summary, $logger) {
                foreach ($orders as $order) {
                    DB::beginTransaction();

                    try {
                        $user  = $order->user;
                        $miner = $order->miner;

                        if (!$user || !$miner) {
                            DB::rollBack();
                            $summary['errors'] += 1;
                            $logger->error('plans:process missing relations', [
                                'order_id' => $order->id,
                                'user_id'  => $order->user_id,
                                'miner_id' => $order->miner_id,
                            ]);
                            continue;
                        }

                        $userCoinBalance = UserCoinBalance::where('user_id', $order->user_id)
                            ->where('miner_id', $order->miner_id)
                            ->lockForUpdate()
                            ->first();

                        if (!$userCoinBalance) {
                            DB::rollBack();
                            $summary['skipped_missing_balance'] += 1;
                            $logger->warning('plans:process skipped missing balance', [
                                'order_id' => $order->id,
                                'user_id'  => $order->user_id,
                                'miner_id' => $order->miner_id,
                            ]);
                            continue;
                        }

                        $returnAmount = rand($order->min_return_per_day * 100000000, $order->max_return_per_day * 100000000) / 100000000;

                        $userBadge          = $user->badge;
                        $boostEarningAmount = 0;

                        if ($userBadge && $userBadge->badge) {
                            $boostPercent       = $userBadge->badge->earning_boost;
                            $boostEarningAmount = $returnAmount * $boostPercent / 100;
                            $returnAmount      += $boostEarningAmount;
                        }

                        $userCoinBalance->balance += $returnAmount;
                        $userCoinBalance->save();

                        $user->profit_wallet += $returnAmount;
                        $user->save();

                        $trx = getTrx();

                        $transaction               = new Transaction();
                        $transaction->user_id      = $order->user_id;
                        $transaction->order_id     = $order->id;
                        $transaction->amount       = $returnAmount;
                        $transaction->post_balance = getAmount($userCoinBalance->balance, 16);
                        $transaction->charge       = 0;
                        $transaction->wallet_type  = Status::PROFIT_WALLET;
                        $transaction->trx_type     = '+';
                        $transaction->details      = 'Daily return amount received';
                        $transaction->trx          = $trx;
                        $transaction->currency     = $miner->currency_code;
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
                        $badgeDiscount   = 0;

                        if ($maintenanceCost && $userBadge && $userBadge->badge) {
                            $badgeDiscount   = $maintenanceCost * $userBadge->badge->discount_maintenance_cost / 100;
                            $maintenanceCost -= $badgeDiscount;
                        }

                        if ($maintenanceCost > 0) {
                            $userCoinBalance->balance -= $maintenanceCost;
                            $userCoinBalance->save();

                            $maintenanceTransaction               = new Transaction();
                            $maintenanceTransaction->order_id     = $order->id;
                            $maintenanceTransaction->user_id      = $order->user_id;
                            $maintenanceTransaction->amount       = $maintenanceCost;
                            $maintenanceTransaction->post_balance = getAmount($userCoinBalance->balance, 16);
                            $maintenanceTransaction->charge       = 0;
                            $maintenanceTransaction->wallet_type  = Status::PROFIT_WALLET;
                            $maintenanceTransaction->trx_type     = '-';
                            $maintenanceTransaction->details      = 'Deducted as maintenance charge';
                            $maintenanceTransaction->trx          = $trx;
                            $maintenanceTransaction->currency     = $miner->currency_code;
                            $maintenanceTransaction->remark       = 'maintenance_cost';
                            $maintenanceTransaction->save();

                            if ($badgeDiscount > 0) {
                                $badgeReward                 = new BadgeReward();
                                $badgeReward->user_id        = $order->user_id;
                                $badgeReward->user_badge_id  = $userBadge->id;
                                $badgeReward->badge_id       = $userBadge->badge_id;
                                $badgeReward->transaction_id = $maintenanceTransaction->id;
                                $badgeReward->amount         = $badgeDiscount;
                                $badgeReward->currency       = $maintenanceTransaction->currency;
                                $badgeReward->remark         = 'discount_maintenance_cost';
                                $badgeReward->save();
                            }
                        }

                        $order->period_remain = max($order->period_remain - 1, 0);
                        $order->last_paid     = Carbon::now();

                        if ($order->period_remain === 0) {
                            $summary['expired'] += 1;
                        }

                        $order->save();

                        DB::commit();

                        $summary['processed'] += 1;
                        $logger->info('plans:process success', [
                            'order_id'       => $order->id,
                            'user_id'        => $order->user_id,
                            'period_remain'  => $order->period_remain,
                            'earned'         => $returnAmount,
                            'maintenance'    => $maintenanceCost ?? 0,
                        ]);
                    } catch (\Throwable $e) {
                        DB::rollBack();
                        $summary['errors'] += 1;
                        $logger->error('plans:process error', [
                            'order_id' => $order->id ?? null,
                            'user_id'  => $order->user_id ?? null,
                            'message'  => $e->getMessage(),
                        ]);
                    }
                }
            });

        $finishedAt = Carbon::now();

        $logger->info('plans:process finished', array_merge($summary, [
            'time' => $finishedAt->toDateTimeString(),
        ]));

        $this->info(sprintf(
            'plans:process processed=%d expired=%d skipped_missing_balance=%d errors=%d',
            $summary['processed'],
            $summary['expired'],
            $summary['skipped_missing_balance'],
            $summary['errors'],
        ));

        return self::SUCCESS;
    }

    private function setUserBadge(User $user): void {
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
            $userBadge->unlocked_at     = Carbon::now();
            $userBadge->save();

            notify($user, 'USER_BADGE_UPGRADE', [
                'username'       => $user->fullname,
                'name'           => $badge->name,
                'earning_amount' => showAmount($badge->earning_amount, currencyFormat: false),
            ]);

            $sequenceNumber += 1;
        }
    }
}
