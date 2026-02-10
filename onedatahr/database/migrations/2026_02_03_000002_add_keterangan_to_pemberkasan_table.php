<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pemberkasan', function (Blueprint $table) {
            if (! Schema::hasColumn('pemberkasan', 'keterangan')) {
                $table->text('keterangan')->nullable()->after('background_checking');
            }
        });
    }

    public function down()
    {
        Schema::table('pemberkasan', function (Blueprint $table) {
            if (Schema::hasColumn('pemberkasan', 'keterangan')) {
                $table->dropColumn('keterangan');
            }
        });
    }
};
