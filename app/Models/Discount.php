<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    protected $table = 'discounts';

    protected $fillable = [
        'user_id',
        'code',
        'status',
        'value_percent',
        'value_max',
        'attempts'
    ];

    public function getStartedAtAttribute($value) {
        if (isset($value))
            return dateConvert($value);
    }

    public function getExpiredAtAttribute($value) {
        if (isset($value))
            return dateConvert($value);
    }

    public function getCreatedAtAttribute($value) {
        if (isset($value))
            return dateConvert($value);
    }

    public function getUpdatedAtAttribute($value) {
        if (isset($value))
            return dateConvert($value);
    }
}
