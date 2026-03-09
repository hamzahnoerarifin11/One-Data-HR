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
        // if (!Schema::hasTable('performance_locks')) {
        //     Schema::create('performance_locks', function (Blueprint $table) {
        //         $table->id();
        //         $table->integer('tahun')->comment('Tahun yang dikunci');
        //         $table->enum('status', ['locked', 'unlocked'])->default('locked')->comment('Status lock');
        //         $table->unsignedBigInteger('locked_by')->nullable()->comment('User yang lock');
        //         $table->timestamp('locked_at')->nullable()->comment('Waktu lock');
        //         $table->unsignedBigInteger('unlocked_by')->nullable()->comment('User yang unlock (hanya superadmin)');
        //         $table->timestamp('unlocked_at')->nullable()->comment('Waktu unlock');
        //         $table->text('locked_reason')->nullable()->comment('Alasan lock');
        //         $table->text('unlock_reason')->nullable()->comment('Alasan unlock');
        //         $table->timestamps();

        //         // Foreign keys
        //         $table->foreign('locked_by')->references('id')->on('users')->onDelete('set null');
        //         $table->foreign('unlocked_by')->references('id')->on('users')->onDelete('set null');

        //         // Unique constraint
        //         $table->unique('tahun');

        //         // Indexes
        //         $table->index('status');
        //         // $table->index('tahun'); // Removing redundant index (unique covers it)
        //         $table->index('locked_at');
        //     });
        // }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performance_locks');
    }
};
