<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';

    protected $fillable = [
        'order_id',
        'user_id',
        'payment_id',
        'value',
        'status'
    ];

    public function getCreatedAtAttribute($value) {
        if (isset($value))
            return dateConvert($value);
    }

    public function getUpdatedAtAttribute($value) {
        if (isset($value))
            return dateConvert($value);
    }
}
