<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Organization scope determines at which level the user can see data
            // 'all' = superadmin/admin (no filter), 'holding', 'company', 'division', 'department', 'unit'
            if (!Schema::hasColumn('users', 'org_scope')) {
                $table->string('org_scope')->default('all')->after('password');
            }
            
            // Organization hierarchy IDs for filtering
            if (!Schema::hasColumn('users', 'holding_id')) {
                $table->unsignedBigInteger('holding_id')->nullable()->after('org_scope');
                $table->foreign('holding_id')->references('id')->on('holdings')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('users', 'company_id')) {
                $table->unsignedBigInteger('company_id')->nullable()->after('holding_id');
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('users', 'division_id')) {
                $table->unsignedBigInteger('division_id')->nullable()->after('company_id');
                $table->foreign('division_id')->references('id')->on('divisions')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('users', 'department_id')) {
                $table->unsignedBigInteger('department_id')->nullable()->after('division_id');
                $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('users', 'unit_id')) {
                $table->unsignedBigInteger('unit_id')->nullable()->after('department_id');
                $table->foreign('unit_id')->references('id')->on('units')->onDelete('set null');
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop foreign keys first
            $foreignKeys = ['holding_id', 'company_id', 'division_id', 'department_id', 'unit_id'];
            foreach ($foreignKeys as $fk) {
                if (Schema::hasColumn('users', $fk)) {
                    $table->dropForeign(['users_' . $fk . '_foreign']);
                    $table->dropColumn($fk);
                }
            }
            
            if (Schema::hasColumn('users', 'org_scope')) {
                $table->dropColumn('org_scope');
            }
        });
    }
};
