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
        Schema::table('tempa_peserta', function (Blueprint $table) {
            if (!Schema::hasColumn('tempa_peserta', 'mentor_id')) {
                $table->unsignedBigInteger('mentor_id')->nullable()->after('nik_karyawan');
                $table->foreign('mentor_id')->references('id')->on('users');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tempa_peserta', function (Blueprint $table) {
            if (Schema::hasColumn('tempa_peserta', 'mentor_id')) {
                $table->dropForeign(['mentor_id']);
                $table->dropColumn('mentor_id');
            }
        });
    }
};
