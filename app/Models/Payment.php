<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payments';

    protected $fillable = [
        'user_id',
        'invoice_id',
        'value',
        'status',
        'code',
        'gateway',
        'information'
    ];

    public function getPaidAtAttribute($value) {
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