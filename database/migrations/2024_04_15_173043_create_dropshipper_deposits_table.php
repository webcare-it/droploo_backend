<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDropshipperDepositsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dropshipper_deposits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dropshipper_id');
            $table->double('amount');
            $table->string('payment_gateway');
            $table->string('transaction_id')->unique();
            $table->integer('status')->default(0)->comment('0=>pending, 1=>approved, 2=>rejected');
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
        Schema::dropIfExists('dropshipper_deposits');
    }
}
