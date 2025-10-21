<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;


class StockTaking extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $table = 'stock_taking';
    protected $fillable = [
        'transaction_id',
        'product_id',
        'variation_id',
        'actual_quantity',
    ];
}
