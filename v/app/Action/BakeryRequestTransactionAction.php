<?php

namespace App\Action;

use App\Contact;
use App\Enums\BakeryLocation;
use Illuminate\Http\Request;

class BakeryRequestTransactionAction
{
    const PANONE_SUPPLIER = 'PANONE MIN SUPERMARKET';

    //Was request From Bakery or For Bakery during purchasing to help handling stock-out
    public function handle(Request $request): bool
    {
        return (($request->has('contact_id') && self::PANONE_SUPPLIER == (Contact::query()->findOrFail($request->contact_id)->name || Contact::query()->findOrFail($request->contact_id)->supplier_business_name)) || ($request->has('is_bakery') && $request->input('is_bakery') == BakeryLocation::IS_BAKERY));
    }
}