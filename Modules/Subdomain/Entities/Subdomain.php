<?php

namespace Modules\Subdomain\Entities;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subdomain extends Model
{
    use HasUuids,SoftDeletes;

    protected $connection = 'core_domain';

    protected $casts = [

    ];

    protected $fillable = [
        'sub_domain',
        'registered_by',
        'db_name',
        'active_modules',
        'db_connection',
        'admin_username',
    ];
}
