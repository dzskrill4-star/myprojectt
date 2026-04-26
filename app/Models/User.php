<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\UserNotify;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable {
    use HasApiTokens, UserNotify;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'ver_code',
        'balance',
        'kyc_data',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'kyc_data'          => 'object',
        'session_data'      => 'object',
        'ver_code_send_at'  => 'datetime',
        'baridi_withdraw'   => 'boolean',
    ];

    public function loginLogs() {
        return $this->hasMany(UserLogin::class);
    }

    public function orders() {
        return $this->hasMany(Order::class)->orderBy('id', 'desc');
    }

    public function badges() {
        return $this->hasMany(UserBadge::class)->orderBy('sequence_number');
    }

    public function badge() {
        return $this->hasOne(UserBadge::class)->orderBy('sequence_number', 'DESC');
    }

    public function paidOrders() {
        return $this->hasMany(Order::class)->where('status', '!=', Status::ORDER_UNPAID)->orderBy('id', 'desc');
    }

    public function transactions() {
        return $this->hasMany(Transaction::class)->orderBy('id', 'desc');
    }

    public function deposits() {
        return $this->hasMany(Deposit::class)->where('status', '!=', Status::PAYMENT_INITIATE);
    }

    public function payments() {
        return $this->hasMany(Deposit::class)->where('status', '!=', Status::PAYMENT_INITIATE)->where('order_id', '>', 0);
    }

    public function withdrawals() {
        return $this->hasMany(Withdrawal::class)->where('status', '!=', Status::PAYMENT_INITIATE);
    }

    public function coinBalances() {
        return $this->hasMany(UserCoinBalance::class);
    }

    public function referrer() {
        return $this->belongsTo(User::class, 'ref_by');
    }

    public function referrals() {
        return $this->hasMany(User::class, 'ref_by')->whereNotNull('username');
    }

    public function allReferrals() {
        return $this->referrals()->with('referrer');
    }

    public function deviceTokens() {
        return $this->hasMany(DeviceToken::class);
    }

    public function supportTickets() {
        return $this->hasMany(SupportTicket::class);
    }

    /**
     * Get unread support messages count
     */
    public function unreadTicketMessagesCount() {
        return SupportMessage::whereHas('ticket', function($query) {
                $query->where('user_id', $this->id);
            })
            ->whereNotNull('admin_id')
            ->where('is_read', 0)
            ->count();
    }

    public function fullname(): Attribute {
        return new Attribute(
            get: fn() => $this->firstname . ' ' . $this->lastname,
        );
    }

    protected function username(): Attribute {
        return new Attribute(
            set: fn($value) => is_string($value) ? strtolower(trim($value)) : $value,
        );
    }

    public function mobileNumber(): Attribute {
        return new Attribute(
            get: fn() => $this->mobile ? ($this->dial_code . $this->mobile) : null,
        );
    }

    // SCOPES
    public function scopeActive($query) {
        return $query->where('status', Status::USER_ACTIVE)->where('ev', Status::VERIFIED)->where('sv', Status::VERIFIED);
    }

    public function scopeBanned($query) {
        return $query->where('status', Status::USER_BAN);
    }

    public function scopeEmailUnverified($query) {
        return $query->where('ev', Status::NO);
    }

    public function scopeMobileUnverified($query) {
        return $query->where('sv', Status::NO);
    }

    public function scopeKycUnverified($query) {
        return $query->where('kv', Status::KYC_UNVERIFIED);
    }

    public function scopeKycPending($query) {
        return $query->where('kv', Status::KYC_PENDING);
    }

    public function scopeEmailVerified($query) {
        return $query->where('ev', Status::VERIFIED);
    }

    public function scopeMobileVerified($query) {
        return $query->where('sv', Status::VERIFIED);
    }

    public function scopeWithBalance($query) {
        return $query->where('balance', '>', 0);
    }

    public function totalReturnedAmount() {
        return Transaction::where('transactions.user_id', $this->id)
            ->leftJoin('miners', 'transactions.currency', '=', 'miners.currency_code')
            ->selectRaw("
            SUM(
                CASE
                    WHEN transactions.remark = 'return_amount' THEN transactions.amount * miners.rate
                    WHEN transactions.remark = 'maintenance_cost' THEN -1 * transactions.amount * miners.rate
                    ELSE 0
                END
            ) as total
        ")->value('total') ?? 0;
    }

    public function totalReferralCommission() {
        return Transaction::where('user_id', $this->id)
            ->where('remark', 'referral_commission')
            ->selectRaw('currency, SUM(amount) as total_amount')
            ->groupBy('currency')
            ->get()
            ->sum('total_amount');
    }

    public function totalEarningAmount() {
        // Mining/Investment earnings only (referral commissions are separate)
        return $this->totalReturnedAmount();
    }
}
