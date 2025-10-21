<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;


final class LabelType extends Enum
{
    const PAYMENT = 'payments';
    const PRODUCT = 'product';
    const CONTACT = 'contact';
}
