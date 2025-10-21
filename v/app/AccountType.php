<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class AccountType extends Model  implements Auditable
{
        use \OwenIt\Auditing\Auditable;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function sub_types()
    {
        return $this->hasMany(\App\AccountType::class, 'parent_account_type_id');
    }

    public function parent_account()
    {
        return $this->belongsTo(\App\AccountType::class, 'parent_account_type_id');
    }
}
