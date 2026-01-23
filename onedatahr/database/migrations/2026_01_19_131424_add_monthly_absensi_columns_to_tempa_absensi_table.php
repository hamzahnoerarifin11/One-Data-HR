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
        Schema::table('tempa_absensi', function (Blueprint $table) {
            // Kolom tahun untuk absensi
            $table->year('tahun_absensi')->after('id_peserta');

            // Kolom absensi bulanan (5 minggu per bulan)
            // Januari
            $table->boolean('jan_1')->default(0)->after('tahun_absensi');
            $table->boolean('jan_2')->default(0)->after('jan_1');
            $table->boolean('jan_3')->default(0)->after('jan_2');
            $table->boolean('jan_4')->default(0)->after('jan_3');
            $table->boolean('jan_5')->default(0)->after('jan_4');

            // Februari
            $table->boolean('feb_1')->default(0)->after('jan_5');
            $table->boolean('feb_2')->default(0)->after('feb_1');
            $table->boolean('feb_3')->default(0)->after('feb_2');
            $table->boolean('feb_4')->default(0)->after('feb_3');
            $table->boolean('feb_5')->default(0)->after('feb_4');

            // Maret
            $table->boolean('mar_1')->default(0)->after('feb_5');
            $table->boolean('mar_2')->default(0)->after('mar_1');
            $table->boolean('mar_3')->default(0)->after('mar_2');
            $table->boolean('mar_4')->default(0)->after('mar_3');
            $table->boolean('mar_5')->default(0)->after('mar_4');

            // April
            $table->boolean('apr_1')->default(0)->after('mar_5');
            $table->boolean('apr_2')->default(0)->after('apr_1');
            $table->boolean('apr_3')->default(0)->after('apr_2');
            $table->boolean('apr_4')->default(0)->after('apr_3');
            $table->boolean('apr_5')->default(0)->after('apr_4');

            // Mei
            $table->boolean('mei_1')->default(0)->after('apr_5');
            $table->boolean('mei_2')->default(0)->after('mei_1');
            $table->boolean('mei_3')->default(0)->after('mei_2');
            $table->boolean('mei_4')->default(0)->after('mei_3');
            $table->boolean('mei_5')->default(0)->after('mei_4');

            // Juni
            $table->boolean('jun_1')->default(0)->after('mei_5');
            $table->boolean('jun_2')->default(0)->after('jun_1');
            $table->boolean('jun_3')->default(0)->after('jun_2');
            $table->boolean('jun_4')->default(0)->after('jun_3');
            $table->boolean('jun_5')->default(0)->after('jun_4');

            // Juli
            $table->boolean('jul_1')->default(0)->after('jun_5');
            $table->boolean('jul_2')->default(0)->after('jul_1');
            $table->boolean('jul_3')->default(0)->after('jul_2');
            $table->boolean('jul_4')->default(0)->after('jul_3');
            $table->boolean('jul_5')->default(0)->after('jul_4');

            // Agustus
            $table->boolean('agu_1')->default(0)->after('jul_5');
            $table->boolean('agu_2')->default(0)->after('agu_1');
            $table->boolean('agu_3')->default(0)->after('agu_2');
            $table->boolean('agu_4')->default(0)->after('agu_3');
            $table->boolean('agu_5')->default(0)->after('agu_4');

            // September
            $table->boolean('sep_1')->default(0)->after('agu_5');
            $table->boolean('sep_2')->default(0)->after('sep_1');
            $table->boolean('sep_3')->default(0)->after('sep_2');
            $table->boolean('sep_4')->default(0)->after('sep_3');
            $table->boolean('sep_5')->default(0)->after('sep_4');

            // Oktober
            $table->boolean('okt_1')->default(0)->after('sep_5');
            $table->boolean('okt_2')->default(0)->after('okt_1');
            $table->boolean('okt_3')->default(0)->after('okt_2');
            $table->boolean('okt_4')->default(0)->after('okt_3');
            $table->boolean('okt_5')->default(0)->after('okt_4');

            // November
            $table->boolean('nov_1')->default(0)->after('okt_5');
            $table->boolean('nov_2')->default(0)->after('nov_1');
            $table->boolean('nov_3')->default(0)->after('nov_2');
            $table->boolean('nov_4')->default(0)->after('nov_3');
            $table->boolean('nov_5')->default(0)->after('nov_4');

            // Desember
            $table->boolean('des_1')->default(0)->after('nov_5');
            $table->boolean('des_2')->default(0)->after('des_1');
            $table->boolean('des_3')->default(0)->after('des_2');
            $table->boolean('des_4')->default(0)->after('des_3');
            $table->boolean('des_5')->default(0)->after('des_4');

            // Kolom kalkulasi
            $table->integer('total_hadir')->default(0)->after('des_5');
            $table->integer('total_pertemuan')->default(0)->after('total_hadir');
            $table->decimal('persentase', 5, 2)->default(0)->after('total_pertemuan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tempa_absensi', function (Blueprint $table) {
            // Drop kolom kalkulasi
            $table->dropColumn(['persentase', 'total_pertemuan', 'total_hadir']);

            // Drop kolom Desember
            $table->dropColumn(['des_1', 'des_2', 'des_3', 'des_4', 'des_5']);

            // Drop kolom November
            $table->dropColumn(['nov_1', 'nov_2', 'nov_3', 'nov_4', 'nov_5']);

            // Drop kolom Oktober
            $table->dropColumn(['okt_1', 'okt_2', 'okt_3', 'okt_4', 'okt_5']);

            // Drop kolom September
            $table->dropColumn(['sep_1', 'sep_2', 'sep_3', 'sep_4', 'sep_5']);

            // Drop kolom Agustus
            $table->dropColumn(['agu_1', 'agu_2', 'agu_3', 'agu_4', 'agu_5']);

            // Drop kolom Juli
            $table->dropColumn(['jul_1', 'jul_2', 'jul_3', 'jul_4', 'jul_5']);

            // Drop kolom Juni
            $table->dropColumn(['jun_1', 'jun_2', 'jun_3', 'jun_4', 'jun_5']);

            // Drop kolom Mei
            $table->dropColumn(['mei_1', 'mei_2', 'mei_3', 'mei_4', 'mei_5']);

            // Drop kolom April
            $table->dropColumn(['apr_1', 'apr_2', 'apr_3', 'apr_4', 'apr_5']);

            // Drop kolom Maret
            $table->dropColumn(['mar_1', 'mar_2', 'mar_3', 'mar_4', 'mar_5']);

            // Drop kolom Februari
            $table->dropColumn(['feb_1', 'feb_2', 'feb_3', 'feb_4', 'feb_5']);

            // Drop kolom Januari
            $table->dropColumn(['jan_1', 'jan_2', 'jan_3', 'jan_4', 'jan_5']);

            // Drop kolom tahun
            $table->dropColumn('tahun_absensi');
        });
    }
};
