<?php

namespace App\Services;

use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

Class ExchangeRatesService
{
    const API_URL = 'https://api.nbp.pl/api';

    public function getExchangeRates(){

        $url = self::API_URL . '/exchangerates/tables/A';

        $request = Http::get($url);
        $currentValue = 0;

        while (!empty($request['0']['rates'][$currentValue]['currency'])){

            $currency=Currency::where('currency_code', '=', $request['0']['rates'][$currentValue]['code'])->first();
            
            if ($currency){
                /**
                 * Updating currency_code field.
                 */
                Currency::where('currency_code', '=', $request['0']['rates'][$currentValue]['code'])->update(['exchange_rate'=>$request['0']['rates'][$currentValue]['mid']]);
            
            } else {

                /**
                * Saving data to database.
                */
                $currency = new Currency;
                $currency->name = $request['0']['rates'][$currentValue]['currency'];
                $currency->currency_code = $request['0']['rates'][$currentValue]['code'];
                $currency->exchange_rate = $request['0']['rates'][$currentValue]['mid'];
    
                $currency->save();
            }

        $currentValue++;

        }
    }
}