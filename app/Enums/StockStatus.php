<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

final class StockStatus extends Enum
{
    const POS_NEG = 'pos_neg';
    const ZERO = 'zero';
    const NEGATIVE = 'negative';
    const  POSITIVE = 'positive';
}
