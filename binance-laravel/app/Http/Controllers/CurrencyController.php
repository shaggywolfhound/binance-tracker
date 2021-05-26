<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UsersToCurrencies;
use Illuminate\Http\Request;
use App\Models\Currencies;
use Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class CurrencyController extends Controller
{
    private $binanceSecret;
    private $binanceApi;
    private $timeStamp;
    private $signature;
    private $response;
    //todo: remove this
    private $id = 2;
    private $window = 5000;


    public function __construct()
    {
        $this->binanceSecret = config('binance.secret');
        $this->binanceApi = config('binance.api');
    }

    public function index() {

        //get user data and currencies
        $user = User::where('id', '=', $this->id)
            ->with('currencies')
            ->with('userscurrencies')
            ->get()->first();

        $currencies = Currencies::where('is_quote', '=', 1)->get();

        return view('currencies', [
            'user'              => $user,
            'quoteCurrencies'   => $currencies
        ]);
    }

    public function patchUserCurrencies(Request $request) {
//        abort(400, 'Parameter must be a string and uppercase');
        if (!is_array($request->input())) {
            abort(400, 'data not present');
        }

        //update database
        foreach($request->input() as $row) {
            $userCurrencies = UsersToCurrencies::where('user_id', '=', $this->id)
                ->where('currency_id', '=', $row['currency_id']);
            if (!$userCurrencies->update($row)){
                abort(400, 'failed to update database');
            }
        }

        //return index page to axios call
        return $this->index();
    }

    /**
     * Gets all currencies from Binance and fills currencies table
     * checks if anything new and adds (takes too long)
     * @return bool
     */
    public function getCurrencies() {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://api.binance.com/api/v3/exchangeInfo");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response  = curl_exec($ch);
            if (!$response) {
                Log::critical('No timestamp', [curl_error($ch)]);
                return false;
            }
        } catch (\Exception $e) {
            Log::critical('No timestamp', [$e]);
            return false;
        }
        $data = json_decode($response);

        //got all currencies now store/update in the database!!
        $currencies = Currencies::all();

        ///have 1 table but with only assets, then link on itself.
        $assets     = [];
        $quote       = [];
        foreach ($data->symbols as $symbol) {
            $quote[]    = $symbol->quoteAsset;
            $assets[]   = $symbol->baseAsset;
            $assets[]   = $symbol->quoteAsset;
        }

        //remove duplicates and reindex
        $assets = array_values(array_unique($assets, SORT_REGULAR));
        $quote  = array_values(array_unique($quote, SORT_REGULAR));

        //check each symbol if in dbase
        foreach ($assets as $key =>$asset) {
            foreach ($currencies as $currency) {
                if ($currency->asset === $asset) {
                    unset($assets[$key]);
                }
            }
        }

        //todo: hate this need to redo somehow
        foreach ($assets as $asset) {
            if (in_array($asset, $quote)) {
                $currencies = Currencies ::create([
                    'asset'     => $asset,
                    'is_quote'  => 1
                ]);
            } else {
                $currencies = Currencies ::create([
                    'asset'     => $asset,
                    'is_quote'  => 0
                ]);
            }

            if (!$currencies->save()){
                Log::critical('database save not working');
                return false;
            }
        }
        return true;
    }


    /**
     * Not in use at the moment but gets /api/v3/account using signature etc
     * @return bool
     */
    public function getUserCurrencies() {

        if (!$this->getBinanceTimeStamp()) {
            Log::info('couldn\'t get timestamp');
        };
        if (!$this->encodeSignature()) {
            Log::info('could not encode signature');
        }
        if (!$this->sendSecureRequest()) {
            Log::info('Request failed');
        }

        if(!$this->storeResult()) {
            Log::info('Not save to database');
        }
        //navigate to currencies page
        return redirect('/currencies');
    }


    /** add result to users_to_currencies table
     * @return bool
     */
    public function storeResult() {
        $data = json_decode($this->response);
        if (json_last_error() != JSON_ERROR_NONE) {
            Log::critical('json would not decode');
            return false;
        }

        $this->data = $data;

        foreach ($data->balances as $item ) {
            //get items with dollar and add to database
            if (!($item->free == 0 && $item->locked == 0 )) {
                //get current currency, if not run getCurrencies()
                // ***how do I connect account values to currency***
                $currency = Currencies::where('asset', '=', $item->asset)->first();
                //todo: need to get current user when launched within frontend
                //!!DON'T DUPLICATE!!
                $currentCurrency = UsersToCurrencies::where('currency_id', '=', $currency->id)->get();

                if ( count($currentCurrency) === 0 ) {
                    $userCurrency = UsersToCurrencies ::create([
                        'currency_id' => $currency->id,
                        'user_id'     => $this->id,
                    ]);
                    $userCurrency -> save();
                }
            }
        }
        return true;
    }

    //todo: must move this to a helper, duplicated code!!!
    public function sendRequest($url) {
        $apiKey = $this->binanceApi;
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://api.binance.com/api/v3/$url");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Content-Type: application/json",
                "X-MBX-APIKEY: $apiKey",
                "Accept: Application/json"
            ));
            $response  = curl_exec($ch);
            if (!$response) {
                Log::critical('Request send error', [curl_error($ch)]);
                return false;
            }
            $this->response = $response;
        } catch (\Exception $e) {
            echo 'ooops';
            Log::critical('request failed', $e);
            return false;
        }

        return true;
    }

    public function sendSecureRequest($query = '') {
        $timeStamp = $this->timeStamp;
        $signature = $this->signature;
        $apiKey = $this->binanceApi;
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://api.binance.com/api/v3/account?timestamp=$timeStamp&signature=$signature");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Content-Type: application/json",
                "X-MBX-APIKEY: $apiKey",
                "Accept: Application/json"
            ));
            $response  = curl_exec($ch);
            if (!$response) {
                Log::critical('Request send error', [curl_error($ch)]);
                return false;
            }
            $this->response = $response;
        } catch (\Exception $e) {
            echo 'ooops';
            Log::critical('request failed', $e);
            return false;
        }

        return true;
    }

    public function encodeSignature($additionalQuery='') {

        if (!$this->timeStamp && !is_string($additionalQuery)) {
            return false;
        }

        $query = 'timestamp='.$this->timeStamp.$additionalQuery;
        $query .= ($additionalQuery === '') ? '' : $query.'&'.$additionalQuery;
        $this->signature = hash_hmac('sha256', $query, $this->binanceSecret);

        return true;
    }

    public function getBinanceTimeStamp(){
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://api.binance.com/api/v3/time");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response  = curl_exec($ch);
            if (!$response) {
                Log::critical('No timestamp', [curl_error($ch)]);
                return false;
            }

            $timeStamp = json_decode($response) -> serverTime;
            curl_close($ch);
            $this -> timeStamp = $timeStamp;
            return true;
        } catch (\Exception $e) {
            Log::critical('No timestamp', [$e]);
            return false;
        }
    }

    public function tester() {
        //get time
        $apiKey = $this->binanceApi;
        $secret = $this->binanceSecret;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.binance.com/api/v3/time");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response  = curl_exec($ch);
        $timeStamp = json_decode($response)->serverTime;
        curl_close($ch);

//include all parts of query string!!
        $query_string = 'timestamp='.$timeStamp;
        $signature = hash_hmac('sha256', $query_string, $secret);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.binance.com/api/v3/myTrades?timestamp=$timeStamp&signature=$signature");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "X-MBX-APIKEY: $apiKey",
            "Accept: Application/json"
        ));
        $response  = curl_exec($ch);

        echo 'status code: ';
        echo curl_getinfo($ch, CURLINFO_HTTP_CODE);
        echo '<br>';

        if(curl_errno($ch)) {
            echo 'Curl error: ' . curl_error($ch);
        }

// Then, after your curl_exec call:
        $info = curl_getinfo($ch);
        print_r($info);

        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);

        if ($response) {
            echo $response;
        } else {
            echo $response;
            echo 'Curl error: ' . curl_error($ch);
        }
        curl_close($ch);
    }

}
