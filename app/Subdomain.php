<?php

namespace App;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;


class Subdomain extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory,HasUuids,SoftDeletes;
    
    protected $connection = 'core_domain';


    protected $casts = [

    ];
    protected $fillable = [
        'sub_domain',
        'env_file',
        'registered_by',
        'db_name',
        'active_modules',
                'db_connection',
    ];
}
