<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCurrenciesTickersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('currencies_tickers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('currency_id')->index();
            $table->unsignedBigInteger('quote_currency_id')->index();

            $table->decimal('price_change', 24, 15)->nullable();
            $table->decimal('last_price', 24, 15)->nullable();

            $table->foreign('currency_id')->references('id')->on('currencies');
            $table->foreign('quote_currency_id')->references('id')->on('currencies');
            $table->foreign('user_id')->references('id')->on('users');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('currency_tickers', function (Blueprint $table) {
            $table->dropForeign(['currency_id']);
            $table->dropForeign(['quote_currency_id']);
            $table->dropForeign(['user_id']);
        });
        Schema::dropIfExists('currency_tickers');
    }
}
