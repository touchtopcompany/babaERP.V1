<?php


namespace App\Exports;

use App\Enums\StockTakingReportTitle;
use App\Exports\StockTakingReport\ExcessReportSheetExport;
use App\Exports\StockTakingReport\LossReportSheetExport;
use App\Exports\StockTakingReport\NegativeReportSheetExport;
use App\Exports\StockTakingReport\OriginalReportSheetExport;
use App\Utils\ProductUtil;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class StockTakingReportExport implements FromArray, WithMultipleSheets,WithEvents
{
    use RegistersEventListeners;

    public function __construct(protected array $exported_data, protected ProductUtil $productUtil)
    {
    }

    public function array(): array
    {
        return $this->exported_data;
    }

    public function sheets(): array
    {
        return [
            new ExcessReportSheetExport($this->exported_data[StockTakingReportTitle::EXCESS]),
            new LossReportSheetExport($this->exported_data[StockTakingReportTitle::LOSS]),
            new NegativeReportSheetExport($this->exported_data[StockTakingReportTitle::NEGATIVE]),
            new OriginalReportSheetExport($this->exported_data[StockTakingReportTitle::ORIGINAL]),
        ];
    }

}