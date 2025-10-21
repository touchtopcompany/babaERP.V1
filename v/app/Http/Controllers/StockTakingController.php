<?php

namespace App\Http\Controllers;

use App\BusinessLocation;
use App\Category;
use App\Exports\StockTakingReportExport;
use App\Exports\StockTakingTemplateExport;
use App\Product;
use App\StockAdjustmentLine;
use App\StockTaking;
use App\Transaction;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\VariationLocationDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class StockTakingController extends Controller
{

    public function __construct(protected ProductUtil $productUtil, protected TransactionUtil $transactionUtil)
    {
    }

    public function getImportInventory()
    {
        if (!auth()->user()->can('product.stock_taking')) {
            abort(403, 'Unauthorized action.');
        }

        $zip_loaded = extension_loaded('zip');

        $business_id = request()->session()->get('user.business_id');
        $business_locations = BusinessLocation::forDropdown($business_id, false);
        $categories = Category::forDropdown($business_id, 'product');
        $categories->prepend(__('lang_v1.none'), 'none');

        //Check if zip extension it loaded or not.
        if ($zip_loaded === false) {
            $output = [
                'success' => 0,
                'msg' => 'Please install/enable PHP Zip archive for import'
            ];

            return view('stock_taking.import')
                ->with('notification', $output);
        } else {
            return view('stock_taking.import', compact('business_locations', 'categories'));
        }
    }

    public function postImportInventory(Request $request)
    {
        if (!auth()->user()->can('product.stock_taking')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            //Set maximum php execution time
            ini_set('max_execution_time', 0);

            $should_export = $request->get('print_stock_report');

            if ($request->hasFile('inventory_csv')) {
                $file = $request->file('inventory_csv');
                $parsed_array = Excel::toArray((object)[], $file);

                //Remove header row
                $imported_data = array_splice($parsed_array[0], 3);
                // if (count($imported_data[0]) != 8) {
                //     throw new \Exception("File format is not correct.Please verify if you import correct file.");
                // };

                $all_data = [];
                $original_data = [];
                $loss_data = [];
                $excess_data = [];
                $negative_data = [];

                $loss_var_value = 0;
                $positive_var_value = 0;

                DB::beginTransaction();
                foreach ($imported_data as $key => $value) {

                    //Number of columns
                    //Expected is 8
                    // if (count($value) != 8) {
                    //     throw new \Exception("Number of columns mismatch,expected is 8 columns.");
                    // }
                    $row_no = $key + 4;

                    //Check if SKU is present
                    if (empty($value[0])) {
                        throw new \Exception("Product SKU is required in row no. $row_no");
                    }
                    $pro_code = $value[0];
                    $pro_name = $value[1];
                    $pro_category = $value[2];
                    $pro_unit = $value[3];

                    $location_name = $value[7];
                    //Location is empty
                    if (empty($location_name)) {
                        throw new \Exception("Product location is required in row no. $row_no");
                    }
                    //Current Stock
                    //Exclude 0 value
                    $online_stock = $value[4];
                    if (!isset($online_stock)) {
                        throw new \Exception("Current stock is required in row no. $row_no");
                    }

                    if (!is_numeric(trim($online_stock))) {
                        throw new \Exception("Please verify all columns with number value");
                    }
                    // if(empty($value[6])){
                    //     continue;
                    // }
                    
                    $actual_quantity = !empty($value[6]) ? $value[6] : 0;
                    if($actual_quantity != 0 && !is_numeric($actual_quantity)){
                        throw new \Exception("Invalid quantity in row no. $row_no");
                    }
                   
                    $business_id = request()->session()->get('business.id');

                    $get_location_id = BusinessLocation::query()->where('name', $location_name)->first('id');
                    if (!$get_location_id) {
                        throw new \Exception("Unrecognized business location $location_name");
                    }

                    $product_details = Product::query()->join('variations as v', 'products.id', '=', 'v.product_id')
                        ->where([
                            'sub_sku' => $pro_code,
                            'business_id' => $business_id,
                            'products.name' => $pro_name
                        ])->first(['products.id as product', 'v.id as variation']);

                    if (!$product_details) {
                        throw new \Exception("Product not {$pro_name} found on row no. $row_no");
                    }

                    $product_id = $product_details['product'];
                    $variation_id = $product_details['variation'];
                    $location_id = $get_location_id->id;

                    // //Let do get the real stock (To avoid cached stock if present)
                    // $stock_details = $this->productUtil->getVariationStockDetails($business_id, $variation_id, $location_id);
                    // $stock_history = $this->productUtil->getVariationStockHistory($business_id, $variation_id, $location_id);

                    // if (isset($stock_history[0]) && (float)$stock_details['current_stock'] != (float)$stock_history[0]['stock']) {
                    //     $online_stock = $stock_history[0]['stock'];
                    // }

                    //Get the last stock adjustment transaction
                    $_adjustment_transactions = Transaction::query()->where([
                        'location_id' => $location_id,
                        'type' => 'stock_adjustment'
                    ])->pluck('id')->toArray();

                    $last_adjustment = StockTaking::query()
                        ->where('variation_id', $variation_id)
                        ->whereIn('transaction_id', $_adjustment_transactions)
                        ->latest()
                        ->first();
                        if($last_adjustment){
                    $last_stock = $last_adjustment->actual_quantity;
                    if ($last_stock == $actual_quantity) {
                        continue;
                    }
                }

                    // $last_stock = $last_adjustment->actual_quantity;
                    // if ($last_stock == $actual_quantity) {
                    //     continue;
                    // }


                    //Check before & after stock to check if it needs update
                    if ($online_stock <= 0 && $actual_quantity >= 0) {
                        $variation_unit = $actual_quantity - $online_stock;
                        //Reset the stock
                        $online_stock = 0;
                    }
                    else{
                        $variation_unit = $actual_quantity - $online_stock;
                    }
                    // dd($variation_unit);
                     if($variation_unit == 0){
                        continue;
                    }
                    $unit_price = $this->productUtil->num_uf($value[5]);
                    $variation_cost_value = $online_stock < 0 ? ($online_stock * $unit_price) : ($variation_unit * $unit_price);
                    if ($online_stock > 0) {
                        $variation_unit > 0 ? $positive_var_value += $variation_cost_value : $loss_var_value += $variation_cost_value;
                    }
                    
                   
                    
                     $ref_count = $this->productUtil->setAndGetReferenceCount('stock_adjustment');
//
//                    //Generate reference number
                    $reference_no = $this->productUtil->generateReferenceNumber('stock_adjustment', $ref_count);

                    //Create this transaction
                    $stock_adjustment = Transaction::query()
                        ->create([
                            'business_id' => $business_id,
                            'location_id' => $location_id,
                            'type' => 'stock_adjustment',
                            'transaction_date' => \Carbon::now(),
                        'ref_no' => $reference_no,
                            'created_by' => request()->session()->get('user.id')
                        ]);
                        
                        //Decrease available quantity
                    // $this->productUtil->decreaseProductQuantity(
                    //   $product_id,
                    //     $variation_id,
                    //   $location_id,
                    //     $this->productUtil->num_uf($variation_unit)
                    // );

                    //Record
                    $stock_taking = StockTaking::query()
                        ->create([
                            'product_id' => $product_id,
                            'variation_id' => $variation_id,
                            'transaction_id' => $stock_adjustment->id,
                            'actual_quantity' => $actual_quantity,
                        ]);
                    $stock_adjustment->update(['stock_taking_id' => $stock_taking->id]);

                    //Record the Stock Adjustment for this product
                    $stock_adjustment->stock_adjustment_lines()->create([
                        'product_id' => $product_id,
                        'variation_id' => $variation_id,
                        'quantity' => $variation_unit,
                        'unit_price' => $unit_price
                    ]);

                    // //Update stock
                    VariationLocationDetails::query()->where([
                        'product_id' => $product_id,
                        'location_id' =>$location_id
                    ])->update([
                        'qty_available' => $actual_quantity,
                    ]);

                    $business = ['id' => $business_id,
                        'accounting_method' => \request()->session()->get('business.accounting_method'),
                        'location_id' => $location_id,
                    ];
                    $this->transactionUtil->mapPurchaseSell($business, $stock_adjustment->stock_adjustment_lines, 'stock_adjustment');

                    //Data export
                    $current_data = [
                        'code' => $pro_code,
                        'name' => $pro_name,
                        'category' => $pro_category,
                        'unit' => $pro_unit,
                        'qty' => $online_stock,
                        'cost' => $unit_price,
                        'actual' => $actual_quantity,
                        'variation' => $variation_unit,
                        'value' => $variation_cost_value,
                        'location' => $location_name,
                    ];

                    $product_get = VariationLocationDetails::query()->where([
                        'product_id' => $product_id,
                        'location_id' => $location_id
                    ])->first();


                    $log = [
                        'code' => $pro_code,
                        'name' => $pro_name,
                        'category' => $pro_category,
                        'previous_stock' => $online_stock,
                        'current_stock' => $actual_quantity,
                        'location' => $location_name
                    ];

                    $this->productUtil->activityLog($product_get, 'update_stock', null, $log);

                    $original_data[] = $current_data;
                    if ($online_stock > 0) {
                        if ($variation_unit > 0) {
                            $excess_data[] = $current_data;
                        } else if ($variation_unit < 0) {
                            $loss_data[] = $current_data;
                        }
                    } else if ($online_stock < 0) {
                        $negative_data[] = $current_data;
                    }

                    $all_data = [
                        'original' => $original_data,
                        'excess' => $excess_data,
                        'loss' => $loss_data,
                        'negative' => $negative_data,
                    ];
                }
                DB::commit();
                $output = $this->respondSuccess(message: 'Successfully', res_json: false);
                return (int)$should_export ==  1 && !empty($all_data) ? self::downloadStockTakingReport($all_data) : redirect()->route('stock-taking.import')->with('notification', $output);
            }
        } catch
        (\Exception $e) {
            DB::rollBack();
            $output = $this->respondWithError($e->getMessage(), false);
            return redirect()->route('stock-taking.import')->with('notification', $output);
        }
    }


    //Download template
    public function downloadInventoryTemplate(Request $request)
    {
        $filename = 'stock-taking-template-' . \Carbon::now()->format('d-m-YH:i') . '.xlsx';
        $location_id = $request->location_id;
        $stock_status = $request->stock_status_id;
        $category = $request->category_id;
        return Excel::download(new StockTakingTemplateExport($stock_status, $location_id, $category, $this->productUtil), $filename);
    }

    //Download report
    private function downloadStockTakingReport($exported_data)
    {
        $filename = 'stock-taking-report-' . \Carbon::now()->format('Y-m-d') . '.xlsx';
        return Excel::download(new StockTakingReportExport($exported_data, $this->productUtil), $filename);
    }

}