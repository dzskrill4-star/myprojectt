<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BadgeReward extends Model {

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function userBadge() {
        return $this->belongsTo(UserBadge::class);
    }

    public function badge() {
        return $this->belongsTo(Badge::class);
    }

    public function transaction() {
        return $this->belongsTo(Transaction::class);
    }
}
