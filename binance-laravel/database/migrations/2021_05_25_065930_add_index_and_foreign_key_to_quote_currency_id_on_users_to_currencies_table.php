<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexAndForeignKeyToQuoteCurrencyIdOnUsersToCurrenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users_to_currencies', function (Blueprint $table) {
            $table->foreign('quote_currency_id')->references('id')->on('currencies');
            $table->index('quote_currency_id');
            $table->index('currency_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users_to_currencies', function (Blueprint $table) {
            $table->dropForeign(['quote_currency_id']);
            $table->dropIndex(['quote_currency_id']);
            $table->dropIndex(['currency_id']);
        });
    }
}
