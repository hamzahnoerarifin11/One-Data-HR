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
        Schema::table('pekerjaan', function (Blueprint $table) {
            $table->dropColumn('Jabatan');
        });
    }

    public function down(): void
    {
        Schema::table('pekerjaan', function (Blueprint $table) {
            $table->enum('Jabatan', ['Staff', 'Kepala Regu', 'Kepala Unit', 'Kepala Shift', 'Head Of Brances (HOB)', 'Supervisor', 'Manager', 'General Manager (GM)', 'Direktur'])->nullable()->after('position_id');
        });
    }
};
