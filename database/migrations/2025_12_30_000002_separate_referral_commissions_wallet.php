<?php

use App\Constants\Status;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Move historical referral commission ledger entries out of PROFIT_WALLET
        // and fix profit_wallet balances so referral is not mixed into earning wallet.

        // Collect user IDs that have referral commission transactions in PROFIT_WALLET
        $userIds = Transaction::where('remark', 'referral_commission')
            ->where('wallet_type', Status::PROFIT_WALLET)
            ->distinct()
            ->pluck('user_id');

        foreach ($userIds as $userId) {
            $commissionSum = (float) Transaction::where('user_id', $userId)
                ->where('remark', 'referral_commission')
                ->where('wallet_type', Status::PROFIT_WALLET)
                ->sum('amount');

            if ($commissionSum <= 0) {
                continue;
            }

            // Subtract from profit_wallet (clamp at 0 to avoid negatives)
            $user = User::find($userId);
            if (!$user) {
                continue;
            }

            $newProfitWallet = (float) $user->profit_wallet - $commissionSum;
            if ($newProfitWallet < 0) {
                $newProfitWallet = 0;
            }
            $user->profit_wallet = $newProfitWallet;
            $user->save();

            // Reclassify wallet type
            Transaction::where('user_id', $userId)
                ->where('remark', 'referral_commission')
                ->where('wallet_type', Status::PROFIT_WALLET)
                ->update(['wallet_type' => Status::REFERRAL_WALLET]);

            // Recalculate post_balance for referral commissions to keep UI consistent.
            $running = 0.0;
            Transaction::where('user_id', $userId)
                ->where('remark', 'referral_commission')
                ->where('wallet_type', Status::REFERRAL_WALLET)
                ->orderBy('id')
                ->chunkById(500, function ($txns) use (&$running) {
                    foreach ($txns as $txn) {
                        $amount = (float) $txn->amount;
                        if ($txn->trx_type === '-') {
                            $running -= $amount;
                        } else {
                            $running += $amount;
                        }
                        DB::table('transactions')->where('id', $txn->id)->update(['post_balance' => $running]);
                    }
                });
        }
    }

    public function down(): void
    {
        // Non-trivial to revert reliably (would require restoring historical profit_wallet values)
    }
};
