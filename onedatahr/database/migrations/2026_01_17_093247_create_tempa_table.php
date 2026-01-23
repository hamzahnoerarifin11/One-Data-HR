<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tempa', function (Blueprint $table) {
            $table->id('id_tempa');
            $table->string('jenis_tempa');
            $table->string('cabang');
            $table->string('nama_tempa');
            $table->string('periode');
            $table->integer('jumlah_pertemuan');
            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tempa');
    }
};
