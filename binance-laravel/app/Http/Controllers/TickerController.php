<?php

namespace App\Http\Controllers;

use App\Models\CurrenciesTickers;
use App\Models\UsersToCurrencies;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

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
        $error = '';
        if (Session::has('error')) {
            $error = Session::get('error');
        }

        $user = User::where('id', '=', $this->id)
                    ->with('currenciestickers')
                    ->get()->first();

        return view('ticker', [
            'user'  => $user,
            'error' => $error
        ]);
    }

    public function getTickers()
    {
        //get all user currencies
        $userSymbols = UsersToCurrencies::where('user_id', '=', $this->id)
                    ->with('quotecurrency')
                    ->with('currency')
                    ->get();

        //convert to symbols = base+quote
        $symbols = [];
        foreach ($userSymbols as $symbol) {
            if (!is_null($symbol->quotecurrency)) {
                $symbols[] = [
                    'symbol'                => $symbol->currency->asset.$symbol->quotecurrency->asset,
                    'currency_id'           => $symbol->currency_id,
                    'quote_currency_id'     => $symbol->quote_currency_id,
                ];
            }
        }

        //check we have trackers
        if (empty($symbols)) {
            abort(204,'No trackers setup');
        }

        foreach ($symbols as $key => $symbol) {
            //make sure result was a symbol
            if (!empty($symbol['symbol']) && !preg_match('/^[A-Z]+$/', $symbol['symbol'])) {
                abort(400, 'Symbols not returned');
            }
            //get data from binanace
            if (!$this->sendRequest('ticker/24hr?symbol='.$symbol['symbol'])) {
                abort(400, 'No response from API');
            } else {

                $symbols[$key]['response'] = json_decode($this->response);
                if (json_last_error() != JSON_ERROR_NONE) {
                    Log::critical('json would not decode');
                    abort(400, 'json would not decode');
                }
                //check to make sure no errors on request
                if(property_exists($symbols[$key]['response'], 'code')) {
                    return redirect()->back()->with(
                        'error',
                        'Could not get ticker ('.$symbols[$key]['symbol'].') : '.$symbols[$key]['response']->msg
                    );
                }
            }
        }

        //store in database
        foreach($symbols as $symbol) {
            $currencyTicker = CurrenciesTickers::create([
                'user_id'               => $this->id,
                'currency_id'           => $symbol['currency_id'],
                'quote_currency_id'     => $symbol['quote_currency_id'],
                'symbol'                => $symbol['symbol'],
                'price_change'          => $symbol['response']->priceChange,
                'last_price'            => $symbol['response']->lastPrice
            ]);
            $currencyTicker->save();
        }

        //navigate to currencies page
        return redirect('ticker');

    }


    //TODO: must move this to a helper, duplicated code!!!
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
