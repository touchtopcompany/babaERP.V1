<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;
final class SellPaymentStatus extends Enum
{
    const PAID = 'paid';
    const DUE = 'due';
    const OVERDUE = 'overdue';
    const PARTIAL = 'partial';
    const OUTSTANDING = 'outstanding';
}
