<?php


namespace App\Exports\StockTakingReport;

use App\Enums\StockTakingReportTitle;
class ExcessReportSheetExport extends ReportSheetExport
{
    public function __construct(protected array $data)
    {
        return parent::__construct($this->data,StockTakingReportTitle::EXCESS);
    }
}