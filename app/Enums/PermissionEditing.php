<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;
final class PermissionEditing extends Enum
{
    const IS_PERMITTED = 1;
    const IS_NOT_PERMITTED = 0;
}
