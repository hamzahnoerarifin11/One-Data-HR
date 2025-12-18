<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('kpi_items', function (Blueprint $table) {
            // Kita tambahkan kolom-kolom yang dipakai di template tapi belum ada di database
            if (!Schema::hasColumn('kpi_items', 'target')) {
                $table->string('target')->nullable()->after('bobot'); // String agar bisa isi "100%" atau "> 50"
            }
            if (!Schema::hasColumn('kpi_items', 'polaritas')) {
                $table->string('polaritas')->nullable()->after('target'); // Maximize / Minimize
            }
            if (!Schema::hasColumn('kpi_items', 'satuan')) {
                $table->string('satuan')->nullable()->after('polaritas'); // %, Jam, Kasus, dll
            }
        });
    }

    public function down()
    {
        Schema::table('kpi_items', function (Blueprint $table) {
            $table->dropColumn(['target', 'polaritas', 'satuan']);
        });
    }
};