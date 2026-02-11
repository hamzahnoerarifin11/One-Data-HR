<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kpi_scores', function (Blueprint $table) {
            // Januari - Juni
            $table->double('target_jan')->nullable()->after('target_smt1');
            $table->double('real_jan')->nullable()->after('target_jan');
            $table->double('target_feb')->nullable()->after('real_jan');
            $table->double('real_feb')->nullable()->after('target_feb');
            $table->double('target_mar')->nullable()->after('real_feb');
            $table->double('real_mar')->nullable()->after('target_mar');
            $table->double('target_apr')->nullable()->after('real_mar');
            $table->double('real_apr')->nullable()->after('target_apr');
            $table->double('target_mei')->nullable()->after('real_apr');
            $table->double('real_mei')->nullable()->after('target_mei');
            $table->double('target_jun')->nullable()->after('real_mei');
            $table->double('real_jun')->nullable()->after('target_jun');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kpi_scores', function (Blueprint $table) {
            $table->dropColumn([
                'target_jan',
                'real_jan',
                'target_feb',
                'real_feb',
                'target_mar',
                'real_mar',
                'target_apr',
                'real_apr',
                'target_mei',
                'real_mei',
                'target_jun',
                'real_jun'
            ]);
        });
    }
};
