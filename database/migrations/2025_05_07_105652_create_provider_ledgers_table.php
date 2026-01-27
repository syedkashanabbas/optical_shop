<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateProviderLedgersTable extends Migration
{
    public function up()
    {
        Schema::create('provider_ledgers', function (Blueprint $table) {
            $table->id();
        
            $table->integer('provider_id')->index(); // match provider's signed int
            $table->foreign('provider_id')->references('id')->on('providers')->onDelete('cascade');
        
            $table->string('type'); // purchase, return, payment
            $table->string('reference')->nullable();
            $table->date('date'); // ← fixed
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('credit', 15, 2)->default(0);
            $table->decimal('balance', 15, 2)->default(0);
            $table->timestamps();
        });
        
    }

    public function down()
    {
        Schema::dropIfExists('provider_ledgers');
    }
}
