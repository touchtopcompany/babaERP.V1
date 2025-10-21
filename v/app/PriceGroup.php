<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;


class PriceGroup extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory,SoftDeletes;

    protected $guarded = ['id'];

    public function scopeActive($query)
    {
        return $query->where('price_groups.is_active', 1);
    }

    /**
     * Return list of selling price groups
     *
     * @param int $business_id
     *
     * @return array
     */
    public static function forDropdown($business_id, $with_default = true)
    {
        cache()->clear();
        $price_groups = cache()->remember('for_dropdown', 100,function () use ($business_id) {
            return PriceGroup::where('business_id', $business_id)
                ->active()
                ->get();
        });

        $dropdown = [];

        if ($with_default && auth()->user()->can('access_default_selling_price')) {
            $dropdown[0] = __('lang_v1.default_selling_price');
        }

        foreach ($price_groups as $price_group) {
            if (auth()->user()->can('price_group.' . $price_group->id)) {
                $dropdown[$price_group->id] = $price_group->name;
            }
        }
        return $dropdown;
    }

    /**
     * Counts total number of selling price groups
     *
     * @param int $business_id
     *
     * @return array
     */
    public static function countPriceGroups($business_id)
    {
        $count = PriceGroup::where('business_id', $business_id)
            ->active()
            ->count();

        return $count;
    }
}
