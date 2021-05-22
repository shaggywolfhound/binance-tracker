<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCurrencyTickers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('currency_tickers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('currency_id');
            $table->unsignedBigInteger('user_id');

            $table->decimal('price_change', 24, 15)->nullable();
            $table->decimal('last_price', 24, 15)->nullable();

            $table->foreign('currency_id')->references('id')->on('currencies');
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
            $table -> dropForeign(['currency_id']);
            $table -> dropForeign(['user_id']);
        });
        Schema::dropIfExists('currency_tickers');
    }
}
