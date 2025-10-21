<?php
namespace Modules\Subdomain\Entities;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class SubUser  extends Model
{
    use HasRoles;
    protected $table = 'users';

    protected $connection = 'sub_domain';

}