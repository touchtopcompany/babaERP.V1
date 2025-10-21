<?php

namespace App\Action;

use App\BusinessLocation;
use App\Enums\SellPaymentStatus;
use App\Enums\TransactionType;
use DB;

class CalculateDebitPaidByCashAction
{
    public function __construct(protected array $filters){}

    /**
     * Calculate the debit amount using purchases and expenses, partially and total paid
     * @return mixed
     */
    public function handle(): mixed
    {
        $debit = DB::table('account_transactions as at')
            ->join('accounts', 'at.account_id', '=', 'accounts.id')
            ->leftJoin('transactions as t', 't.id', '=', 'at.transaction_id')
            ->join('business_locations as bl', 'bl.id', '=', 't.location_id')
            ->whereIn('t.type', [TransactionType::EXPENSE, TransactionType::PURCHASE])
            ->whereIn('t.payment_status', [SellPaymentStatus::PAID, SellPaymentStatus::PARTIAL])
            ->whereNull('at.deleted_at')
            ->where([
                't.business_id' => request()->session()->get('user.business_id'),
                'at.type' => 'debit',
            ])
            ->where('accounts.name', 'LIKE', '%cash%')
            ->select(
                DB::raw("SUM(at.amount) as total_debit"),
                DB::raw("SUM(IF(t.type = 'expense', at.amount,0)) as total_expenses"),
                DB::raw("SUM(IF(t.type = 'purchase', at.amount,0)) as total_purchases"),
            );

        // Filter by permitted locations (if set)
        if (!empty($this->filters['permitted_location'])) {
            $debit->whereIn('t.location_id', $this->filters['permitted_location']);
        }

        // Filter by specific location and match cash accounts involved in transactions at that location
        if (!empty($this->filters['location_id'])) {
            $location_id = $this->filters['location_id'];

            $debit->where('t.location_id', $location_id)
                ->whereExists(function ($query) use ($location_id) {
                    $query->select(DB::raw(1))
                        ->from('transactions as tx')
                        ->join('account_transactions as atx', 'atx.transaction_id', '=', 'tx.id')
                        ->whereRaw('atx.account_id = accounts.id')
                        ->where('tx.location_id', $location_id);
                });
        }

        // Filter by operation date
        if (!empty($this->filters['start_date']) && !empty($this->filters['end_date'])) {
            $debit->whereDate('at.operation_date', '>=', $this->filters['start_date'])
                ->whereDate('at.operation_date', '<=', $this->filters['end_date']);
        }


        return $debit->first(['total_debit','total_expenses','total_purchases']);
    }
}