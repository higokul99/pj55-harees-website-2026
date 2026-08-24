<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerOrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'product_code',
        'table_name',
        'quantity',
        'price',
        'metal_cost',
        'making_charges',
        'gst',
        'metal_type'
    ];
}
