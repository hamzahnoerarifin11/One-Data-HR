<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHoldingsTable extends Migration
{
    public function up()
    {
        Schema::create('holdings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['Yayasan', 'Manufaktur'])->default('Yayasan');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('holdings');
    }
}
