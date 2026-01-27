<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustomerNameAndProductCodeToProductLedgersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
     Schema::table('product_ledgers', function (Blueprint $table) {
        $table->string('customer_name')->nullable();
        $table->string('product_code')->nullable();
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
        $table->dropColumn(['customer_name', 'product_code']);
    });
    }
}
