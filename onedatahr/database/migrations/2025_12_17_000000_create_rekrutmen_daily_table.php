<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rekrutmen_daily', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('posisi_id');
            $table->date('date');
            $table->integer('count')->default(0);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->unique(['posisi_id','date']);
            $table->foreign('posisi_id')->references('id_posisi')->on('posisi')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rekrutmen_daily');
    }
};
