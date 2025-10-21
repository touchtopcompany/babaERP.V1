<?php

namespace App\Exports;

use App\BusinessLocation;
use App\Utils\ProductUtil;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


/**
 */
class StockTakingTemplateExport implements FromArray, WithColumnWidths, WithStyles, WithEvents, ShouldAutoSize
{
    public function __construct(protected $stock_status, protected $location_id, protected $category, protected ProductUtil $productUtil)
    {
    }

    public function array(): array
    {
        $business_id = request()->session()->get('business.id');
        $business_name =  request()->session()->get('business.name');
        $filters['location_id'] = $this->location_id;
        $filters['stock_status_id'] = $this->stock_status;
        $filters['category_id'] =  $this->category;
         $filters['active_state'] =  'active';

        $products = $this->productUtil->getProductStockDetails($business_id, $filters, 'view_product');
        $location_name = BusinessLocation::getLocationName($filters['location_id']);

        $products_array = [['STOCK TAKING TEMPLATE SHEET'], ['PRODUCT FOUND AT ' . strtoupper($location_name)], ['Product Code', 'Product Description','Department', 'Unit','Qty','Unit Cost',  'Actual Qty','Location',]];

        foreach ($products as $product) {
            $appends_products_array = [
                $product->sku,
                $product->product,
                $product->category_name,
                $product->unit,
                $product->stock,
                $this->productUtil->num_f($product->purchase_price),
                '',
                ucwords(strtolower($location_name)),

            ];
            $products_array[] = $appends_products_array;
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
        } catch (Exception $e) {
        }

        return [
            // Style the rows as bold text.
            1 => ['font' => ['bold' => true, 'size' => 16]],
            2 => ['font' => ['bold' => true]],
            3 => ['font' => ['bold' => true]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $event->sheet->getDelegate()->getRowDimension('1')->setRowHeight(25);
                $event->sheet->getDelegate()->getRowDimension('2')->setRowHeight(20);

                $event->sheet->getDelegate()->getStyle('A1:H1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle('A2:H2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            },
        ];
    }
}
