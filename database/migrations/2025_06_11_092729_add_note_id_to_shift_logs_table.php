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
        Schema::table('shift_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('note_id')->nullable()->after('id');
            $table->foreign('note_id')->references('id')->on('notes')->onDelete('set null');
            $table->enum('requisition', ['no', 'yes'])->default('no')->after('mark_as_complete');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shift_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('note_id')->nullable()->after('id');
            $table->foreign('note_id')->references('id')->on('notes')->onDelete('set null');
        });
    }
};
