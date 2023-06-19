<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    const ORDER_SKU_PREFIX = 'os-';
    const ORDER_STATUS_COMPLETE = 1;
    const ORDER_STATUS_PENDING = 2;
    const TABLE_NAME = 'orders';

    protected $fillable = [
        'order_sku',
        'customer_id',
        'product_id',
        'qty',
        'price',
        'remark',
        'status_id'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
