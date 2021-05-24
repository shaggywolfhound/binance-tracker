<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsTrackedColumnToUsersToCurrenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users_to_currencies', function (Blueprint $table) {
            $table->unsignedBigInteger('quote_currency_id')->nullable(true)->after('user_id');
            $table->boolean('is_tracked')->nullable(true)->after('quote_currency_id');
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
            $table->dropColumn(['is_tracked']);
            $table->dropColumn(['quote_currency_id']);
        });
    }
}
