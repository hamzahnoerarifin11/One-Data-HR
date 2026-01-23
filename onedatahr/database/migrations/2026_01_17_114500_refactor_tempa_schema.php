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
        // Disable foreign key checks to avoid constraint errors
        Schema::disableForeignKeyConstraints();

        // Drop existing foreign keys and columns
        if (Schema::hasTable('tempa_peserta')) {
            Schema::table('tempa_peserta', function (Blueprint $table) {
                if (Schema::hasColumn('tempa_peserta', 'mentor_id')) {
                    $table->dropColumn('mentor_id');
                }
            });
        }

        if (Schema::hasTable('tempa_kelompok')) {
            Schema::dropIfExists('tempa_kelompok');
        }

        // Recreate tempa_kelompok with correct schema (without foreign keys for now)
        Schema::create('tempa_kelompok', function (Blueprint $table) {
            $table->id('id_kelompok');
            $table->bigInteger('id_tempa')->unsigned();
            $table->string('nama_kelompok');
            $table->string('nama_mentor'); // Just a name, not a user reference
            $table->bigInteger('ketua_tempa_id')->unsigned()->nullable(); // FK to users.id
            $table->timestamps();
        });

        // Re-enable foreign key checks
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('tempa_kelompok');
        Schema::enableForeignKeyConstraints();
    }
};
