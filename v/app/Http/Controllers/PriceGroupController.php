<?php

namespace App\Http\Controllers;

use App\Category;
use App\PriceGroup;
use App\Utils\Util;
use App\TaxRate;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Variation;
use App\VariationGroupPrice;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;

class PriceGroupController extends Controller
{
    protected $commonUtil;

   
    public function __construct(Util $commonUtil, protected ProductUtil $productUtil)
    {
        $this->commonUtil = $commonUtil;
    }

    public function index()
    {
        if (!auth()->user()->can('product.create')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $price_groups = PriceGroup::where('business_id', $business_id)
                ->select(['name', 'description', 'id', 'is_active']);

            return Datatables::of($price_groups)
                ->addColumn(
                    'action',
                    '<button data-href="{{action(\'App\Http\Controllers\PriceGroupController@edit\', [$id])}}" class="btn btn-xs btn-primary btn-modal" data-container=".view_modal"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                        &nbsp;
                        <button data-href="{{action(\'App\Http\Controllers\PriceGroupController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete_spg_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                        &nbsp;
                        <button data-href="{{action(\'App\Http\Controllers\PriceGroupController@activateDeactivate\', [$id])}}" class="btn btn-xs @if($is_active) btn-danger @else btn-success @endif activate_deactivate_spg"><i class="fas fa-power-off"></i> @if($is_active) @lang("messages.deactivate") @else @lang("messages.activate") @endif</button>'
                )
                ->removeColumn('is_active')
                ->removeColumn('id')
                ->rawColumns([2])
                ->make(false);
        }

        return view('price_group.index');
    }


    public function create()
    {
        if (!auth()->user()->can('product.create')) {
            abort(403, 'Unauthorized action.');
        }

        return view('price_group.create');
    }

    public function store(Request $request)
    {
        if (!auth()->user()->can('product.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['name', 'description']);
            $business_id = $request->session()->get('user.business_id');
            $input['business_id'] = $business_id;

            $spg = PriceGroup::create($input);

            //Create a new permission related to the created selling price group
            Permission::query()->firstOrCreate(['name' => 'price_group.' . $spg->id]);

            $output = ['success' => true,
                'data' => $spg,
                'msg' => __("lang_v1.added_success")
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = ['success' => false,
                'msg' => __("messages.something_went_wrong")
            ];
        }

        return $output;
    }

    public function edit($id)
    {
        if (!auth()->user()->can('product.create')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $spg = PriceGroup::where('business_id', $business_id)->find($id);

            return view('price_group.edit')
                ->with(compact('spg'));
        }
    }

    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('product.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $input = $request->only(['name', 'description']);
                $business_id = $request->session()->get('user.business_id');

                $spg = PriceGroup::where('business_id', $business_id)->findOrFail($id);
                $spg->name = $input['name'];
                $spg->description = $input['description'];
                $spg->save();

                $output = ['success' => true,
                    'msg' => __("lang_v1.updated_success")
                ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

                $output = ['success' => false,
                    'msg' => __("messages.something_went_wrong")
                ];
            }

            return $output;
        }
    }

    public function destroy($id)
    {
        if (!auth()->user()->can('product.create')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->user()->business_id;

                $spg = PriceGroup::where('business_id', $business_id)->findOrFail($id);
                $spg->delete();

                $output = ['success' => true,
                    'msg' => __("lang_v1.deleted_success")
                ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

                $output = ['success' => false,
                    'msg' => __("messages.something_went_wrong")
                ];
            }

            return $output;
        }
    }

    public function updateProductPrice(){
        if (! auth()->user()->can('product.update')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = \request()->session()->get('user.business_id');
        $price_groups_for_dropdown = PriceGroup::forDropdown($business_id);
        $categories = Category::forDropdown($business_id, 'product');

        return view('price_group.update_product_price', compact('price_groups_for_dropdown','categories'));
    }
        public function export(Request $request)
    {
        $price_group_id = $request->input('price_group_id');
        $categories_array = $request->input('category_id');
        $business_id = request()->user()->business_id;
        $variations = Variation::join('products as p', 'variations.product_id', '=', 'p.id')
            ->join('product_variations as pv', 'variations.product_variation_id', '=', 'pv.id')
            ->where('p.business_id', $business_id)
            ->whereIn('p.type', ['single', 'variable'])
            ->select('sub_sku', 'p.name as product_name', 'variations.name as variation_name', 'p.type', 'variations.id', 'pv.name as product_variation_name', 'sell_price_inc_tax', 'dpp_inc_tax')
            ->with(['group_prices']);

        if (!empty($categories_array)) {
            $variations->whereIn('category_id', $categories_array);
        }

        $variations = $variations->get();
        $export_data = [];
        foreach ($variations as $variation) {
            $temp = [];
            $temp['Product'] = $variation->type == 'single'
                ? $variation->product_name
                : $variation->product_name . ' - ' . $variation->product_variation_name . ' - ' . $variation->variation_name;

            $temp['Sku (Product Code)'] = $variation->sub_sku;
            $temp['Purchasing Price (Including Tax)'] = $variation->dpp_inc_tax;
            $temp['Selling Price (Including Tax)'] = $variation->sell_price_inc_tax;

            if (!empty($price_group_id) && $price_group_id != 0) {
                $_price_group = PriceGroup::query()->findOrFail($price_group_id)->select('name')->first();
                $_variation_pg = $variation->group_prices->filter(function ($item) use ($price_group_id) {
                    return $item->price_group_id == $price_group_id;
                });
                $temp[$_price_group->name . '-PP'] = $_variation_pg->isNotEmpty() ?? $_variation_pg->first()->purchase_price_inc_tax;
                $temp[$_price_group->name . '-SP'] = $_variation_pg->isNotEmpty() ?? $_variation_pg->first()->sell_price_inc_tax;
            }
            $export_data[] = $temp;
        }
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return collect($export_data)->downloadExcel(
            'product_group_prices.xlsx',
            null,
            true
        );
    }

    // public function export(Request $request)
    // {
    //     $price_group_id = $request->input('price_group_id');
    //     $categories_array = $request->input('category_id');
    //     $business_id = request()->user()->business_id;
    //     $variations = Variation::join('products as p', 'variations.product_id', '=', 'p.id')
    //         ->join('product_variations as pv', 'variations.product_variation_id', '=', 'pv.id')
    //         ->where('p.business_id', $business_id)
    //         ->whereIn('p.type', ['single', 'variable'])
    //         ->select('sub_sku', 'p.name as product_name', 'variations.name as variation_name', 'p.type', 'variations.id', 'pv.name as product_variation_name', 'sell_price_inc_tax', 'dpp_inc_tax')
    //         ->with(['group_prices']);

    //     if (!empty($categories_array)) {
    //         $variations->whereIn('category_id', $categories_array);
    //     }

    //     $variations = $variations->get();
    //     $export_data = [];
    //     foreach ($variations as $variation) {
    //         $temp = [];
    //         $temp['Product'] = $variation->type == 'single'
    //             ? $variation->product_name
    //             : $variation->product_name . ' - ' . $variation->product_variation_name . ' - ' . $variation->variation_name;

    //         $temp['Sku (Product Code)'] = $variation->sub_sku;
    //         $temp['Purchasing Price'] = $variation->dpp_inc_tax;
    //         $temp['Selling Price'] = $variation->sell_price_inc_tax;

    //         if (!empty($price_group_id)) {
    //             $_price_group = PriceGroup::query()->findOrFail($price_group_id)->select('name')->first();
    //             $_variation_pg = $variation->group_prices->filter(function ($item) use ($price_group_id) {
    //                 return $item->price_group_id == $price_group_id;
    //             });
    //             $temp[$_price_group->name . '-PP'] = $_variation_pg->isNotEmpty() ?? $_variation_pg->first()->purchase_price_inc_tax;
    //             $temp[$_price_group->name . '-SP'] = $_variation_pg->isNotEmpty() ?? $_variation_pg->first()->sell_price_inc_tax;
    //         }
    //         $export_data[] = $temp;
    //     }
    //     if (ob_get_contents()) ob_end_clean();
    //     ob_start();
    //     return collect($export_data)->downloadExcel(
    //         'product_group_prices.xlsx',
    //         null,
    //         true
    //     );
    // }
    /**
     * Imports the uploaded file to database.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    // public function import(Request $request)
    // {
    //     try {

    //         $notAllowed = $this->commonUtil->notAllowedInDemo();
    //         if (!empty($notAllowed)) {
    //             return $notAllowed;
    //         }

    //         //Set maximum php execution time
    //         ini_set('max_execution_time', 0);
    //         ini_set('memory_limit', -1);

    //         if ($request->hasFile('product_group_prices')) {
    //             $file = $request->file('product_group_prices');

    //             $parsed_array = Excel::toArray((object)[], $file);

    //             $headers = $parsed_array[0][0];

    //             //Remove header row
    //             $imported_data = array_splice($parsed_array[0], 1);

    //             $business_id = request()->user()->business_id;
    //             $price_groups = PriceGroup::where('business_id', $business_id)->active()->get();

    //             //Get price group names from headers
    //             $_pg_from_headers = [];
    //             foreach ($headers as $key => $value) {
    //                 if (!empty($value) && $key > 3) {
    //                     $_pg_from_headers[$key] = $value;
    //                 }
    //             }

    //             \DB::beginTransaction();
    //             foreach ($imported_data as $key => $value) {
    //                 $variation = Variation::where('sub_sku', $value[1])
    //                     ->first();
    //                 if (empty($variation)) {
    //                     $row = $key + 1;
    //                     $error_msg = __('lang_v1.product_not_found_exception', ['sku' => $value[1], 'row' => $row]);
    //                     throw new \Exception($error_msg);
    //                 }

    //                 foreach ($_pg_from_headers as $k => $v) {
    //                     $price_grp_name = explode('-', $v)[0];
    //                     $price_group = $price_groups->filter(function ($item) use ($price_grp_name) {
    //                         return strtolower($item->name) == strtolower($price_grp_name);
    //                     });
    //                     if ($price_group->isNotEmpty()) {
    //                         //Check if price is numeric
    //                         if (!is_null($value[$k]) && !is_numeric($value[$k])) {
    //                             $row = $key + 1;
    //                             $error_msg = __('lang_v1.price_group_non_numeric_exception', ['row' => $row]);
    //                             throw new \Exception($error_msg);
    //                         }

    //                         //Selling and Purchase Price
    //                         $selling = !empty($value[5]) ? $value[5] : 0;
    //                         $purchase = !empty($value[4]) ? $value[4] : 0;

    //                         if (!empty($value)) {
    //                             VariationGroupPrice::query()->updateOrCreate(
    //                                 ['variation_id' => $variation->id,
    //                                     'price_group_id' => $price_group->first()->id
    //                                 ],
    //                                 [
    //                                     'sell_price_inc_tax' => $selling,
    //                                     'purchase_price_inc_tax' => $purchase,
    //                                 ]
    //                             );
    //                         } else {
    //                             $row = $key + 1;
    //                             $error_msg = __('lang_v1.price_group_not_found_exception', ['pg' => $v, 'row' => $row]);

    //                             throw new \Exception($error_msg);
    //                         }
    //                     }
    //                 }
    //                 \DB::commit();
    //             }
    //             $output = ['success' => 1,
    //                 'msg' => __('lang_v1.product_prices_imported_successfully')
    //             ];
    //         }
    //     }catch (\Exception $e) {
    //         \DB::rollBack();
    //         \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
    //         $output = ['success' => 0,
    //             'msg' => $e->getMessage()
    //         ];
    //     }

    //     return redirect('update-product-price')->with('status', $output);
    // }
    
        public function import(Request $request)
    {
        try {

            $notAllowed = $this->commonUtil->notAllowedInDemo();
            if (!empty($notAllowed)) {
                return $notAllowed;
            }

            //Set maximum php execution time
            ini_set('max_execution_time', 0);
            ini_set('memory_limit', -1);

            if ($request->hasFile('product_group_prices')) {
                $file = $request->file('product_group_prices');

                $parsed_array = Excel::toArray((object)[], $file);

                $headers = $parsed_array[0][0];

                //Remove header row
                $imported_data = array_splice($parsed_array[0], 1);

                $business_id = request()->user()->business_id;
                $price_groups = PriceGroup::where('business_id', $business_id)->active()->get();

                //Get price group names from headers
                $_pg_from_headers = [];
                foreach ($headers as $key => $value) {
                    if (!empty($value) && $key > 3) {
                        $_pg_from_headers[$key] = $value;
                    }
                }

                \DB::beginTransaction();
                foreach ($imported_data as $key => $value) {
                    $variation = Variation::with('product')->where('sub_sku', $value[1])
                        ->first();
                    if (empty($variation)) {
                        $row = $key + 1;
                        $error_msg = __('lang_v1.product_not_found_exception', ['sku' => $value[1], 'row' => $row]);
                        throw new \Exception($error_msg);
                    }

                    //Have price different from default price
                    $log_activity = false;
                    if(!empty($_pg_from_headers)){
                        foreach ($_pg_from_headers as $k => $v) {
                            $price_grp_name = explode('-', $v)[0];
                            $price_group = $price_groups->filter(function ($item) use ($price_grp_name) {
                                return strtolower($item->name) == strtolower($price_grp_name);
                            });
                            if ($price_group->isNotEmpty()) {
                                //Check if price is numeric
                                if (!is_null($value[$k]) && !is_numeric($value[$k])) {
                                    $row = $key + 1;
                                    $error_msg = __('lang_v1.price_group_non_numeric_exception', ['row' => $row]);

                                }

                                //Selling and Purchase Price
                                $selling = !empty($value[5]) ? $value[5] : 0;
                                $purchase = !empty($value[4]) ? $value[4] : 0;

                                if (!empty($value)) {
                                   
                                    VariationGroupPrice::query()->updateOrCreate(
                                        ['variation_id' => $variation->id,
                                            'price_group_id' => $price_group->first()->id
                                        ],
                                        [
                                            'sell_price_inc_tax' => $selling,
                                            'purchase_price_inc_tax' => $purchase,
                                        ]
                                    );

                                    $old_p = $variation->dpp_inc_tax;
                                    $old_s = $variation->sell_price_inc_tax;
                                    $new_p = $purchase;
                                    $new_s = $selling;

                                    $log_activity = true;


                                } else {
                                    $row = $key + 1;
                                    $error_msg = __('lang_v1.price_group_not_found_exception', ['pg' => $v, 'row' => $row]);

                                    throw new \Exception($error_msg);
                                }
                            }
                        }
                    }

                    else{
                        //Update Default price
                        //New Or Same Selling and Purchase Price
                        $new_sp = !empty($value[3]) ? $value[3] : 0;
                        $dpp_inc_tax = !empty($value[2]) ? $value[2] : 0;

                        //Existing price
                        $dpp_exc_tax = $variation->default_purchase_price;
                        $profit_margin = $variation->profit_percent;

                       //Calculate based on the price from the upload
                        $uploaded_profit_margin = 0;
                        if($new_sp > 0 && $dpp_inc_tax > 0){
                            $uploaded_profit_margin = (($new_sp - $dpp_inc_tax) / $dpp_inc_tax) * 100;
                        }

                        $default_profit_percent = $request->session()->get('business.default_profit_percent');
                        if($default_profit_percent > $profit_margin){
                            $profit_margin = $default_profit_percent;
                        }

                        if($uploaded_profit_margin > $profit_margin){
                            $profit_margin = $uploaded_profit_margin;
                        }


                        $tax_percent = ! empty($variation->product->product_tax->amount) ? $variation->product->product_tax->amount : 0;

                        $tax_type = $variation->product->tax_type;

                        $product_prices = $this->calculateVariationPrices($dpp_exc_tax, $dpp_inc_tax, $new_sp, $tax_percent, $tax_type, $profit_margin);

                        $old_p = $variation->dpp_inc_tax;
                        $old_s = $variation->sell_price_inc_tax;
                        $new_p = $dpp_inc_tax;
                        $new_s = $new_sp;

                        Variation::query()
                            ->where(['id' => $variation->id])->update([
                            'default_purchase_price' => $this->productUtil->num_uf($product_prices['dpp_exc_tax']),
                            'dpp_inc_tax' => $this->productUtil->num_uf($product_prices['dpp_inc_tax']),
                            'default_sell_price' => $this->productUtil->num_uf($product_prices['dsp_exc_tax']),
                            'sell_price_inc_tax' => $this->productUtil->num_uf($product_prices['dsp_inc_tax']),
                            'profit_percent' => $profit_margin,
                        ]);

                        $log_activity = true;
                    }
                    if ($log_activity) {
                        $this->productUtil->activityLog($variation->product, 'price_updated', null, [
                            'product' => [
                                'name' => $variation->product->name,
                                'sku' => $variation->sub_sku
                            ],
                            'price_changes' => [
                                'old_purchase' => $old_p,
                                'old_selling' => $old_s,
                                'new_purchase' => $new_p,
                                'new_selling' => $new_s,
                            ]

                        ]);
                    }

                    \DB::commit();
                }
                $output = ['success' => 1,
                    'msg' => __('lang_v1.product_prices_imported_successfully')
                ];
            }
        }catch (\Exception $e) {
            \DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = ['success' => 0,
                'msg' => $e->getMessage()
            ];
        }

        return redirect('update-product-price')->with('status', $output);
    }


    /**
     * Activate/deactivate selling price group.
     *
     */
    public function activateDeactivate($id)
    {
        if (!auth()->user()->can('product.create')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $spg = PriceGroup::where('business_id', $business_id)->find($id);
            $spg->is_active = $spg->is_active == 1 ? 0 : 1;
            $spg->save();

            $output = ['success' => true,
                'msg' => __("lang_v1.updated_success")
            ];

            return $output;
        }
    }
        private function calculateVariationPrices($dpp_exc_tax, $dpp_inc_tax, $selling_price, $tax_amount, $tax_type, $margin)
    {

        //Calculate purchase prices
        if ($dpp_inc_tax == 0) {
            $dpp_inc_tax = $this->productUtil->calc_percentage(
                $dpp_exc_tax,
                $tax_amount,
                $dpp_exc_tax
            );
        }

        if ($dpp_exc_tax == 0) {
            $dpp_exc_tax = $this->productUtil->calc_percentage_base($dpp_inc_tax, $tax_amount);
        }

        if ($selling_price != 0) {
            if ($tax_type == 'inclusive') {
                $dsp_inc_tax = $selling_price;
                $dsp_exc_tax = $this->productUtil->calc_percentage_base(
                    $dsp_inc_tax,
                    $tax_amount
                );
            } elseif ($tax_type == 'exclusive') {
                $dsp_exc_tax = $selling_price;
                $dsp_inc_tax = $this->productUtil->calc_percentage(
                    $selling_price,
                    $tax_amount,
                    $selling_price
                );
            }
        } else {
            $dsp_exc_tax = $this->productUtil->calc_percentage(
                $dpp_exc_tax,
                $margin,
                $dpp_exc_tax
            );
            $dsp_inc_tax = $this->productUtil->calc_percentage(
                $dsp_exc_tax,
                $tax_amount,
                $dsp_exc_tax
            );
        }

        return [
            'dpp_exc_tax' => $this->productUtil->num_f($dpp_exc_tax),
            'dpp_inc_tax' => $this->productUtil->num_f($dpp_inc_tax),
            'dsp_exc_tax' => $this->productUtil->num_f($dsp_exc_tax),
            'dsp_inc_tax' => $this->productUtil->num_f($dsp_inc_tax),
        ];
    }

}
