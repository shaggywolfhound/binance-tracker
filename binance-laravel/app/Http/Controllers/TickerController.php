<?php

namespace App\Http\Controllers;

use App\Models\CurrencyTickers;
use App\Models\UsersToCurrencies;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class TickerController extends Controller
{
    private $binanceSecret;
    private $binanceApi;
    private $id = 2;

    public function __construct()
    {
        $this->binanceSecret = config('binance.secret');
        $this->binanceApi = config('binance.api');
    }

    public function index()
    {
        $user = User::where('id', '=', Auth::id())
                    ->with('currencytickers')
                    ->get()->first();

        return view('ticker', [
            'user' => $user
        ]);
    }

    public function getTickers($symbol='')
    {

        $user = User::where('id', '=', $this->id)
                    ->with('currencies')
                    ->whereHas('currencies', function($query) {
                        $query->where('is_tracked', '=', 1);
                    })
                    ->with('quotecurrencies')
                    ->whereHas('currencies', function($query) {
                        $query->where('is_tracked', '=', 1);
                    })
                    ->get()->first();

        $userSymbols = UsersToCurrencies::where('user_id', '=', $this->id)
                    ->with('quotecurrency')
                    ->with('currency')
                    ->get();

        $symbols = [];
        foreach ($userSymbols as $symbol) {
            echo 'currency: '.$symbol->currency->asset;
            echo ',';
            if (!is_null($symbol->quotecurrency)) {
                echo 'quotecurrency:' . $symbol -> quotecurrency -> asset;
            }
            echo ';';
        }




        if (!empty($symbol) && !preg_match('/^[A-Z]+$/', $symbol)) {
            abort(400, 'Parameter must be a string and uppercase');
        }

        if (!empty($symbol)) {
            if (!$this->sendRequest("ticker/24hr?symbol=$symbol")) {
                abort(400, 'No response from API');
            };
            dd(json_decode($this->response));

        } else {
            if (!$this->sendRequest("ticker/24hr")) {
                abort(400, 'No response from API');
            };
        }
        dd(json_decode($this->response));
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


}
