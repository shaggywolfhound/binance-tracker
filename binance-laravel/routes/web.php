<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CurrencyController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
/*Route::get('/', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');*/

Route::middleware(['auth'])->group(function () {

    //need to sort removal of this could be confusing
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/', function () {
        return view('dashboard');
    })->name('dashboard');

    //currences setup
    Route::get('/currencies', [CurrencyController::class, 'index'])->name('currencies');

});

//todo: must move these into protected and add user
Route::get('/currencies/new', [CurrencyController::class, 'getCurrencies']);
Route::get('/currencies/ticker/{symbol?}', [CurrencyController::class, 'getCurrencyTickers']);
//not used but might be useful in the end
Route::get('/currencies/user', [CurrencyController::class, 'getUserCurrencies']);
Route::get('/tester', [CurrencyController::class, 'tester']);

require __DIR__.'/auth.php';
