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
        Schema::table('onboarding_karyawan', function (Blueprint $table) {
            if (!Schema::hasColumn('onboarding_karyawan', 'jobdesk')) {
                $table->boolean('jobdesk')->default(false)->after('k3');
            }
            if (!Schema::hasColumn('onboarding_karyawan', 'ojt')) {
                $table->boolean('ojt')->default(false)->after('jobdesk');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('onboarding_karyawan', function (Blueprint $table) {
            if (Schema::hasColumn('onboarding_karyawan', 'ojt')) {
                $table->dropColumn('ojt');
            }
            if (Schema::hasColumn('onboarding_karyawan', 'jobdesk')) {
                $table->dropColumn('jobdesk');
            }
        });
    }
};
