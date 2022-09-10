<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    protected $fillable = [
        'product_id',
        'category_id',
        'title',
        'description',
        'price',
        'sku',
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
