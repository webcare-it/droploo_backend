<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDropshippersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dropshippers', function (Blueprint $table) {
            $table->id();
            $table->double('total_deposit')->default(0);
            $table->double('total_credit')->default(0);
            $table->string('name');
            $table->string('user_name')->unique();
            $table->string('domain_name');
            $table->string('phone');
            $table->string('email');
            $table->string('password');
            $table->longText('address');
            $table->string('image')->nullable();
            $table->string('app_key')->unique()->nullable();
            $table->string('app_secret')->unique()->nullable();
            $table->boolean('is_approved')->default(false);
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
        Schema::dropIfExists('dropshippers');
    }
}
