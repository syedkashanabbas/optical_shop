<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountLedgersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_ledgers', function (Blueprint $table) {
          $table->id();
    $table->unsignedBigInteger('account_id');
    $table->string('type'); // e.g., "payment", "adjustment"
    $table->string('reference')->nullable(); // Optional reference
    $table->date('date');
    $table->decimal('debit', 15, 2)->default(0);
    $table->decimal('credit', 15, 2)->default(0);
    $table->decimal('balance', 15, 2)->default(0);
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
        Schema::dropIfExists('account_ledgers');
    }
}
