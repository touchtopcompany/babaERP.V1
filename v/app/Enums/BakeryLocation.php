<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

final class BakeryLocation extends Enum
{
    public const IS_BAKERY = 1;
    public const IS_NOT_BAKERY = 0;
}
