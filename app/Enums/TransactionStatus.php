<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class TransactionStatus extends Enum
{
   const IN_TRANSIT = 'in_transit';
   const PENDING = 'pending';
   const PARTIAL = 'partial';
   const COMPLETED = 'completed';
   const FINALLIZED = 'final';
   const EDITED = 'edited';

   const RECEIVED = 'received';
}
