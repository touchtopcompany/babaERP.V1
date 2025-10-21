<?php

namespace App\Utils;

use App\Currency;
use GuzzleHttp\Client;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MultiCurrencyUtil extends Util
{
    public function __construct(protected string $table = 'multi_currencies_settings')
    {}

    public function multiCurrencies(): Builder
    {
        $business_id = request()->session()->get('user.business_id');

        return DB::table($this->table)
            ->join('currencies',$this->table.'.currency_id', 'currencies.id')
            ->where('business_id', $business_id);
    }

    protected function defaultBusinessCurrency(): Builder
    {
        return  DB::table('currencies')->where('id', session('business.currency_id'));
    }

    protected function rawCurrencyColumnSelect($query)
    {
        return $query->orderBy('country')->select
        (
            'currencies.id as id',
            DB::raw("concat(country, ' - ',currency, '(', code, ') ') as info"),
            'symbol',
            'code',
        );
    }

    public function pluckCurrencies($with_rates = false, $multi_id = null)
    {
        $select_default_currency = $this->rawCurrencyColumnSelect($this->defaultBusinessCurrency());

        if(multiCurrencyEnabled()) {
            $select_multi_currencies = $this->rawCurrencyColumnSelect($this->multiCurrencies());
            $raw_query = $select_multi_currencies;


            if (!empty($multi_id)) {
                $raw_query->where('multi_currencies_settings.id', $multi_id);
            }

            $raw_query = $raw_query->addSelect('exchange_rate AS rate');
        }
        else{
            $raw_query= $select_default_currency;
        }


        $result = $raw_query->get();

        $info = $result->pluck('info', 'id');

        if (!empty($multi_id)) {
            return $result->first();
        }

        if ($with_rates) {
            $rates = collect($result)->mapWithKeys(function ($item) {
                return [$item->id =>
                    [
                        'data-rates' => $item->rate,
                        'data-code' => $item->code,
                        'data-symbol' => $item->symbol,
                    ]
                ];
            })->all();

            return ['info' => $info, 'rates' => $rates];
        }

        return $info;
    }

    public function multiCurrenciesCount(): int
    {
        return multiCurrencyEnabled() ? $this->multiCurrencies()->count() : 0;
    }

    public function convertFromToCurrencies(mixed $from, mixed $to)
    {
        $client = new Client();

        /*
         * RESPONSE TIME IS TOO SLOW OR EVEN TIMEOUT
         */
//        $response = $client->request('GET', 'https://currency-exchange.p.rapidapi.com/exchange?from=' . $from . '&to=' . $to . '&q=1.0', [
//            'headers' => [
//                'X-RapidAPI-Host' => 'currency-exchange.p.rapidapi.com',
//                'X-RapidAPI-Key' => 'a8fe1f9491msh761f5b693e76606p125057jsn861a1c777578',
//            ],
//        ]);

        $response = $client->request('GET', 'https://currency-conversion-and-exchange-rates.p.rapidapi.com/convert?from=' . $from . '&to=' . $to . '&amount=1', [
            'headers' => [
                'X-RapidAPI-Host' => 'currency-conversion-and-exchange-rates.p.rapidapi.com',
                'X-RapidAPI-Key' => 'a8fe1f9491msh761f5b693e76606p125057jsn861a1c777578',
            ],
        ]);

        return json_decode($response->getBody());
    }
}