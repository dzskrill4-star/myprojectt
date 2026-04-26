<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Constants\Status;

class Transaction extends Model {
    public function user() {
        return $this->belongsTo(User::class);
    }

    public function walletType() {
        if ($this->wallet_type == Status::CRYPTO_WALLET) {
            return __('Coin Wallet');
        } elseif ($this->wallet_type == Status::PROFIT_WALLET) {
            return __('Earning Wallet');
        } elseif ($this->wallet_type == Status::REFERRAL_WALLET) {
            return __('Ref. Commissions');
        } else {
            return __('Deposit Wallet');
        }
    }

    public function badgeReward(){
        return $this->hasOne(BadgeReward::class);
    }
}
