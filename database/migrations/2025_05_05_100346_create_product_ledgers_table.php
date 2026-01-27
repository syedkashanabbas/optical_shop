<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductLedgersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_ledgers', function (Blueprint $table) {
            $table->id();

            // Match products.id (which is a signed INT)
            $table->integer('product_id'); // NOT unsigned

            $table->string('type'); // adjustment, sale, purchase, etc
            $table->string('reference')->nullable(); // INV#123, ADJ#55, etc
            $table->date('date');
            $table->integer('quantity_in')->default(0);
            $table->integer('quantity_out')->default(0);
            $table->integer('balance')->nullable(); // optional
            $table->timestamps();

            // Foreign key must match exactly
            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_ledgers');
    }
}
