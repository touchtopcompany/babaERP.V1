<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class TransferRoute extends Enum
{
    const ALL_ROUTES = 'all';
    const TO_ROUTE = 'to';
    const FROM_ROUTE = 'from';
}
