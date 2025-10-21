<?php


namespace App\Exports\StockTakingReport;
use App\Enums\StockTakingReportTitle;

class OriginalReportSheetExport extends ReportSheetExport
{
    public function __construct(protected array $_data)
    {
        return parent::__construct($this->_data, StockTakingReportTitle::ORIGINAL);
    }

}