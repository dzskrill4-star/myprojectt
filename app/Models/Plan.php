<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use GlobalStatus;

    protected $guarded = ['id'];
    protected $hidden  = ['created_at', 'updated_at'];
    protected $casts   = [
        'features' => 'array',
    ];

    public function scopeActive($query)
    {
        $query->where('status', Status::ENABLE);
    }

    public function miner()
    {
        return $this->belongsTo(Miner::class);
    }
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function getPeriodUnitTextAttribute()
    {
        switch ($this->period_unit) {
            case 2:
                return 'Year';
            case 1:
                return 'Month';

            default:
                return 'Day';
        }
    }

    public function getReturnPerDayAttribute()
    {
        if (!$this->max_return_per_day) {
            return showAmount($this->min_return_per_day, 8, exceptZeros: true, currencyFormat: false);
        } else {
            return showAmount($this->min_return_per_day, 8, exceptZeros: true, currencyFormat: false) . ' - ' . showAmount($this->max_return_per_day, 8, exceptZeros: true, currencyFormat: false);
        }
    }

    public function getSpeedUnitTextAttribute()
    {
        switch ($this->speed_unit) {
            case 8:
                return 'Year';
            case 7:
                return 'ZH/s';
            case 6:
                return 'EH/s';
            case 5:
                return 'PH/s';
            case 4:
                return 'TH/s';
            case 3:
                return 'GH/s';
            case 2:
                return 'MH/s';
            case 1:
                return 'KH/s';

            default:
                return 'H/s';
        }
    }
}
