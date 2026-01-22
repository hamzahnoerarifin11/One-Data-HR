<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        // Add columns to tempa_kelompok from tempa
        Schema::table('tempa_kelompok', function (Blueprint $table) {
            if (!Schema::hasColumn('tempa_kelompok', 'jenis_tempa')) {
                $table->string('jenis_tempa')->after('nama_mentor');
            }
            if (!Schema::hasColumn('tempa_kelompok', 'cabang')) {
                $table->string('cabang')->after('jenis_tempa');
            }
            if (!Schema::hasColumn('tempa_kelompok', 'nama_tempa')) {
                $table->string('nama_tempa')->after('cabang');
            }
            if (!Schema::hasColumn('tempa_kelompok', 'periode')) {
                $table->string('periode')->after('nama_tempa');
            }
            if (!Schema::hasColumn('tempa_kelompok', 'jumlah_pertemuan')) {
                $table->integer('jumlah_pertemuan')->after('periode');
            }
            if (!Schema::hasColumn('tempa_kelompok', 'created_by_tempa')) {
                $table->unsignedBigInteger('created_by_tempa')->nullable()->after('jumlah_pertemuan');
                $table->foreign('created_by_tempa')->references('id')->on('users');
            }
        });

        // Copy data from tempa to tempa_kelompok if id_tempa exists
        if (Schema::hasColumn('tempa_kelompok', 'id_tempa') && Schema::hasTable('tempa')) {
            DB::statement("
                UPDATE tempa_kelompok tk
                INNER JOIN tempa t ON tk.id_tempa = t.id_tempa
                SET tk.jenis_tempa = t.jenis_tempa,
                    tk.cabang = t.cabang,
                    tk.nama_tempa = t.nama_tempa,
                    tk.periode = t.periode,
                    tk.jumlah_pertemuan = t.jumlah_pertemuan,
                    tk.created_by_tempa = t.created_by
            ");
        }

        // Add id_kelompok to tempa_materi
        Schema::table('tempa_materi', function (Blueprint $table) {
            if (!Schema::hasColumn('tempa_materi', 'id_kelompok')) {
                $table->unsignedBigInteger('id_kelompok')->nullable()->after('id_tempa');
                $table->foreign('id_kelompok')->references('id_kelompok')->on('tempa_kelompok');
            }
        });

        // Copy id_kelompok to tempa_materi (using the first kelompok per tempa) if id_tempa exists in both
        if (Schema::hasColumn('tempa_materi', 'id_tempa') && Schema::hasColumn('tempa_kelompok', 'id_tempa')) {
            DB::statement("
                UPDATE tempa_materi tm
                SET tm.id_kelompok = (
                    SELECT MIN(tk.id_kelompok)
                    FROM tempa_kelompok tk
                    WHERE tk.id_tempa = tm.id_tempa
                )
            ");
        }

        // Drop foreign keys and id_tempa columns
        if (Schema::hasColumn('tempa_kelompok', 'id_tempa')) {
            DB::statement('ALTER TABLE tempa_kelompok DROP FOREIGN KEY IF EXISTS tempa_kelompok_ibfk_1');
            Schema::table('tempa_kelompok', function (Blueprint $table) {
                $table->dropColumn('id_tempa');
            });
        }

        if (Schema::hasColumn('tempa_peserta', 'id_tempa')) {
            DB::statement('ALTER TABLE tempa_peserta DROP FOREIGN KEY IF EXISTS tempa_peserta_ibfk_1');
            Schema::table('tempa_peserta', function (Blueprint $table) {
                $table->dropColumn('id_tempa');
            });
        }

        if (Schema::hasColumn('tempa_materi', 'id_tempa')) {
            DB::statement('ALTER TABLE tempa_materi DROP FOREIGN KEY IF EXISTS tempa_materi_ibfk_1');
            Schema::table('tempa_materi', function (Blueprint $table) {
                $table->dropColumn('id_tempa');
            });
        }

        // Drop the tempa table
        Schema::dropIfExists('tempa');

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: Reversing this migration is complex and may not restore data accurately.
        // For production, consider backing up data before running this migration.
        Schema::disableForeignKeyConstraints();

        // Recreate tempa table
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

        // Add back id_tempa to tables
        Schema::table('tempa_kelompok', function (Blueprint $table) {
            $table->unsignedBigInteger('id_tempa')->after('id_kelompok');
            $table->foreign('id_tempa')->references('id_tempa')->on('tempa');
        });

        Schema::table('tempa_peserta', function (Blueprint $table) {
            $table->unsignedBigInteger('id_tempa')->after('id_peserta');
            $table->foreign('id_tempa')->references('id_tempa')->on('tempa');
        });

        Schema::table('tempa_materi', function (Blueprint $table) {
            $table->unsignedBigInteger('id_tempa')->after('id_materi');
            $table->foreign('id_tempa')->references('id_tempa')->on('tempa');
        });

        // Note: Data restoration would require complex logic, omitted for simplicity.

        Schema::enableForeignKeyConstraints();
    }
};
