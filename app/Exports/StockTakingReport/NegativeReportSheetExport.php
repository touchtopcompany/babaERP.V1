<?php


namespace App\Exports\StockTakingReport;
use App\Enums\StockTakingReportTitle;

class NegativeReportSheetExport extends ReportSheetExport
{
    public function __construct(protected array $_data)
    {
        return parent::__construct($this->_data, StockTakingReportTitle::NEGATIVE);
    }
}