<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

final class StockTakingReportTitle extends Enum
{
    const LOSS = 'loss';
    const NEGATIVE = 'negative';
    const EXCESS = 'excess';
    const ORIGINAL = 'original';
}
