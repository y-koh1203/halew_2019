<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Sat extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sat', function (Blueprint $table) {
            $table->increments('id');
            $table->string('class_id');
            $table->string('1')->nullable();
            $table->string('2')->nullable();
            $table->string('3')->nullable();
            $table->string('4')->nullable();
            $table->string('5')->nullable();
            $table->string('6')->nullable();
            $table->string('year');
            $table->string('status');
            $table->string('hash')->unique();
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
        Schema::dropIfExists('sat');
    }
}
