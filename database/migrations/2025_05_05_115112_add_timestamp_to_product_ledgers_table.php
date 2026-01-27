<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class AddTimestampToProductLedgersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_ledgers', function (Blueprint $table) {
            $table->timestamp('logged_at')->default(DB::raw('CURRENT_TIMESTAMP'))->after('balance');
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_ledgers', function (Blueprint $table) {
            $table->dropColumn('logged_at');
        });
    }
}
