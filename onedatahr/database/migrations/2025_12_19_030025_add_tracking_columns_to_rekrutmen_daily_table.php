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
        Schema::table('rekrutmen_daily', function (Blueprint $table) {
            $table->integer('lolos_cv')->default(0)->after('total_pelamar');
            $table->integer('lolos_psikotes')->default(0)->after('lolos_cv');
            $table->integer('lolos_kompetensi')->default(0)->after('lolos_psikotes');
            $table->integer('lolos_hr')->default(0)->after('lolos_kompetensi');
            $table->integer('lolos_user')->default(0)->after('lolos_hr');
            $table->text('notes')->nullable()->after('lolos_user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rekrutmen_daily', function (Blueprint $table) {
            // Hapus kolom-kolom yang ditambahkan di method up()
            $table->dropColumn([
                'lolos_cv',
                'lolos_psikotes',
                'lolos_kompetensi',
                'lolos_hr',
                'lolos_user',
                'notes'
            ]);
        });
    }
};