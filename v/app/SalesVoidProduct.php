<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SalesVoidProduct extends Model
{
    protected $table = 'void_sales';
    protected $fillable = [
        'variation_id',
        'amount_removed',
        'quantity_removed',
        'removed_by',
        'location_id'
    ];
}
