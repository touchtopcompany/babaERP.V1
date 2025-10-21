<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ProductVariation extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function variations()
    {
        return $this->hasMany(\App\Variation::class);
    }

    public function variation_template()
    {
        return $this->belongsTo(\App\VariationTemplate::class);
    }
}
