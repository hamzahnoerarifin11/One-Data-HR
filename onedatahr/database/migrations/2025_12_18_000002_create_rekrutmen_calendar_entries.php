<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('rekrutmen_calendar_entries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('posisi_id');
            $table->unsignedBigInteger('kandidat_id')->nullable();
            $table->string('candidate_name')->nullable();
            $table->date('date');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index(['posisi_id','date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('rekrutmen_calendar_entries');
    }
};
