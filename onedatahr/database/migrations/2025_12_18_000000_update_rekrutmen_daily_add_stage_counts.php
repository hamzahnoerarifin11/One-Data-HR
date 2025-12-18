<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rekrutmen_daily', function (Blueprint $table) {
            // Add explicit per-stage counters used by the calendar UI
            $table->integer('total_pelamar')->default(0)->after('date');
            $table->integer('lolos_cv')->default(0)->after('total_pelamar');
            $table->integer('lolos_psikotes')->default(0)->after('lolos_cv');
            $table->integer('lolos_kompetensi')->default(0)->after('lolos_psikotes');
            $table->integer('lolos_hr')->default(0)->after('lolos_kompetensi');
            $table->integer('lolos_user')->default(0)->after('lolos_hr');
        });

        // migrate any legacy `count` values into `total_pelamar`
        if (Schema::hasTable('rekrutmen_daily')) {
            \Illuminate\Support\Facades\DB::table('rekrutmen_daily')
                ->whereNotNull('count')
                ->where('count','>',0)
                ->update(['total_pelamar' => \Illuminate\Support\Facades\DB::raw('count')]);
        }
    }

    public function down(): void
    {
        Schema::table('rekrutmen_daily', function (Blueprint $table) {
            $table->dropColumn(['total_pelamar','lolos_cv','lolos_psikotes','lolos_kompetensi','lolos_hr','lolos_user']);
        });
    }
};
