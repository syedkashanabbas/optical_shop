<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsLedgersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    Schema::create('clients_ledgers', function (Blueprint $table) {
        $table->bigIncrements('id');
        $table->integer('client_id');
        $table->string('type'); // sale, return, payment, etc.
        $table->string('reference')->nullable();
        $table->timestamp('date')->useCurrent();
        $table->decimal('debit', 15, 2)->default(0);   // what client owes
        $table->decimal('credit', 15, 2)->default(0);  // what client paid
        $table->decimal('balance', 15, 2)->default(0); // running total
  
        $table->timestamps();

        $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clients_ledgers');
    }
}
