<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class Invoice extends Model
{
    
    protected $table = 'invoices';

    protected $fillable = [
        'invoice_id',
        'user_id',
        'product_id',
        'value',
        'value_discount',
        'discount_id',
        'is_paid',
        'attempts'
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