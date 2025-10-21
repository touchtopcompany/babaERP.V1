<?php


namespace App\Exports\StockTakingReport;


use App\Enums\StockTakingReportTitle;
use App\Utils\ProductUtil;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportSheetExport implements FromArray, WithColumnWidths, WithStyles, WithEvents, ShouldAutoSize, WithTitle
{
    protected int $negative_variation;
    protected int $length;

    public function __construct(protected array $data, protected string $title)
    {
    }

    public function array(): array
    {

        $_array = $this->data;
        if (count($_array) > 0) {
            $location_name = strtoupper($_array[0]['location']);
            $products_array = [[strtoupper( request()->session()->get('business.name'))], ['STOCK TAKING REPORT FOR ' . ($location_name)], ['Product Code', 'Product Description', 'Category', 'Unit', 'Qty', 'Actual Qty', 'Var', 'Unit Cost', 'Value']];

        }
        else {
            $products_array = [[strtoupper( request()->session()->get('business.name'))], ['NO DATA FOUND'], ['Product Code', 'Product Description', 'Category', 'Unit', 'Qty', 'Actual Qty', 'Var', 'Unit Cost', 'Value']];
        }
        $total_variation = 0;
        $tot_qty = 0;
        $tot_act = 0;
        $tot_var = 0;
        $this->negative_variation = 0;

        $this->length = count($_array) + 6;
        foreach ($_array as $report) {
            $total_variation += $report['value'];
            if ($this->title == 'negative') {
                $this->negative_variation += $report['value'];
            }

            $tot_qty += $report['qty'];
            $tot_act += $report['actual'];
            $tot_var += $report['variation'];
            $appends_products_array = [
                $report['code'],
                $report['name'],
                $report['category'],
                $report['unit'],
                $report['qty'],
                $report['actual'] ?? 0,
                $report['variation'],
                $report['cost'],
                $report['value'],
            ];
            $products_array[] = $appends_products_array;
        }


        if ($this->title == StockTakingReportTitle::ORIGINAL) {
            $products_array[] = [[], [], ['SHOP COST VARIATION ', '', '', '', $tot_qty, $tot_act, $tot_var, '', (new ProductUtil())->num_f($total_variation - 2 * (session('negative')), true)]];
        } else {
            if ($this->title == StockTakingReportTitle::NEGATIVE) {
                session(['negative' => $this->negative_variation]);
            }
            $products_array[] = [[], [], ['TOTAL', '', '', '', $tot_qty, $tot_act, $tot_var, '', (new ProductUtil())->num_f($total_variation, true)]];

        }
        return $products_array;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 40,
            'C' => 20,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        try {
            $sheet->mergeCells('A1:H1');
            $sheet->mergeCells('A2:H2');
            $sheet->mergeCells('A'.$this->length.':D'.$this->length);
        } catch (Exception $e) {
        }

        return [
            // Style the rows as bold text.
            1 => ['font' => ['bold' => true, 'size' => 18]],
            2 => ['font' => ['bold' => true]],
            3 => ['font' => ['bold' => true]],
            $this->length => ['font' => ['bold' => true]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->getRowDimension('1')->setRowHeight(30);
                $event->sheet->getDelegate()->getRowDimension('2')->setRowHeight(25);
                $event->sheet->getDelegate()->getRowDimension($this->length)->setRowHeight(25);

                $event->sheet->getDelegate()->getStyle('A1:H1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle('A2:H2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle('A'.$this->length.':D'.$this->length)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            },
        ];
    }

    public function title(): string
    {
        return $this->title;
    }
}
