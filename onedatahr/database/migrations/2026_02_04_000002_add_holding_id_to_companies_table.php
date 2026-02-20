<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHoldingIdToCompaniesTable extends Migration
{
    public function up()
    {
        // Schema::table('companies', function (Blueprint $table) {
        //     $table->foreignId('holding_id')->nullable()->constrained('holdings')->nullOnDelete();
        // });
    }

    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropConstrainedForeignId('holding_id');
        });
    }
}
