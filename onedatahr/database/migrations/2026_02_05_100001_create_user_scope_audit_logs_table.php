<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_scope_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('changed_by')->nullable();
            $table->string('old_org_scope')->nullable();
            $table->string('new_org_scope')->nullable();
            $table->unsignedBigInteger('old_holding_id')->nullable();
            $table->unsignedBigInteger('new_holding_id')->nullable();
            $table->unsignedBigInteger('old_company_id')->nullable();
            $table->unsignedBigInteger('new_company_id')->nullable();
            $table->unsignedBigInteger('old_division_id')->nullable();
            $table->unsignedBigInteger('new_division_id')->nullable();
            $table->unsignedBigInteger('old_department_id')->nullable();
            $table->unsignedBigInteger('new_department_id')->nullable();
            $table->unsignedBigInteger('old_unit_id')->nullable();
            $table->unsignedBigInteger('new_unit_id')->nullable();
            $table->string('action')->default('update'); // 'create', 'update'
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('changed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_scope_audit_logs');
    }
};
