<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;


class StockAdjustmentLine extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function variation()
    {
        return $this->belongsTo(\App\Variation::class, 'variation_id');
    }

    public function lot_details()
    {
        return $this->belongsTo(\App\PurchaseLine::class, 'lot_no_line_id');
    }
}
