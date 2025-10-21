<?php

namespace App\Http\Controllers;

use App\Business;
use App\Currency;
use App\Utils\MultiCurrencyUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Yajra\DataTables\Facades\DataTables;
use function Symfony\Component\String\s;

class MultiCurrencyController extends Controller
{
    public function __construct(protected MultiCurrencyUtil $multiUtil, protected string $table = 'multi_currencies_settings'){
    }

    public function getSettings(Request $request)
    {

        if (!multiCurrencyEnabled() || auth()->user()->cannot('access_multi_currency_settings')) {
            abort(403, 'Unauthorized action.');
        }


        if($request->ajax()){
            $business_id = request()->session()->get('user.business_id');
            
            $settings = \DB::table($this->table)
                ->where('business_id', $business_id);

            // <button data-href="{{action(\'App\Http\Controllers\MultiCurrencyController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete_multi_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
//                        &nbsp;'

            return   Datatables::of($settings)
                ->addColumn('currency', function ($row){
                    $currency = Currency::findOrFail($row->currency_id);
                    return $currency->country .'-'. $currency->currency;
                })
                ->addColumn('exchange_rate',function ($row){
                    return $row->exchange_rate;
                })
                ->addColumn('exchange_rate_type',function ($row){
                    return $row->exchange_rate_type;
                })
                ->addColumn(
                    'action',
                    '<button data-href="{{action(\'App\Http\Controllers\MultiCurrencyController@edit\', [$id])}}" class="btn btn-xs btn-info btn-modal" data-container=".view_modal"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>'
                        )
                ->make();
        }

        return view('multi_currency.index');
    }

    public function create()
    {
        if (!multiCurrencyEnabled() || auth()->user()->cannot('access_multi_currency_settings')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $business = Business::where('id', $business_id)->first();

        //Created Exchange
        $multi = $this->multiUtil->multiCurrencies()->get()->pluck('id');

        $currencies = Currency::select('id', DB::raw("concat(country, ' - ',currency, '(', code, ') ') as info"))
            ->whereNotIn('id', $multi)
            ->orderBy('country')
            ->pluck('info', 'id');

        return view('multi_currency.create',compact('currencies','business'));
    }

    public function store(Request $request)
    {
        if ($request->ajax()) {
            $output = [
                'success' => true,
                'msg' => __("lang_v1.added_success")
            ];

            $selected_curr = $request->get('currency_id');
            $default_curr = $request->get('default_currency_id');
            if($selected_curr == $default_curr){
                return $output;
            }

            $this->saveMultiCurrencyRequest(request: $request);
            return $output;
        }
    }

    public function edit($id)
    {
        $currency = $this->multiUtil->pluckCurrencies(false, $id);

        return view('multi_currency.edit', compact( 'currency'));
    }

    public function update(Request $request, $id)
    {
        if ($request->ajax()) {
            $output = [
                'success' => true,
                'msg' => __("lang_v1.added_success")
            ];
            $this->saveMultiCurrencyRequest(request: $request);
            return $output;
        }
    }


    public function destroy($id)
    {
        if (!auth()->user()->hasRole('Admin#' . session('business.id'))) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->user()->business_id;

                DB::table($this->table)
                    ->where('business_id', $business_id)
                    ->where('id', $id)->delete();

                $output = ['success' => true,
                    'msg' => __("lang_v1.deleted_success")
                ];
            } catch (\Exception $e) {
                $log = "File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage();
                $output = ['success' => false,
                    'msg' => $log
                ];
            }

            return $output;
        }
    }

    public function APIExchangeRate(Request $request)
    {
        if ($request->ajax()) {
            $selected_curr = $request->get('selected_currency');

            $business_id = request()->session()->get('user.business_id');
            $business = Business::where('id', $business_id)->first('currency_id');

            $default_currency = Currency::findOrFail($business->currency_id)->code;
            $selected_currency = Currency::findOrFail($selected_curr)->code;

            //Get the API Exchange
            $response = $this->multiUtil->convertFromToCurrencies(from: $selected_currency, to: $default_currency);

            return response()->json(['exchange_rate' => $response->result]);

        }
    }

    protected function saveMultiCurrencyRequest(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $exchange_rate = $request->get('exchange_rate');
        $dynamic = $request->get('dynamic_exchange_rate');
        $selected_curr = $request->get('currency_id');

        $data = [
            'business_id' => $business_id,
            'exchange_rate' => $exchange_rate
        ];
        $dynamic
            ? $data['exchange_rate_type'] = 'api'
            : $data['exchange_rate_type'] = 'fixed';

        \DB::table($this->table)->updateOrInsert(
            ['currency_id' => $selected_curr, 'business_id' => $business_id]
            ,$data);
    }
}