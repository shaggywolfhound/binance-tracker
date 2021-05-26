<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSymbolToCurrenciesTickersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('currencies_tickers', function (Blueprint $table) {
            $table->string('symbol')->nullable()->after('quote_currency_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('currencies_tickers', function (Blueprint $table) {
            $table->dropColumn(['symbol']);
        });
    }
}
