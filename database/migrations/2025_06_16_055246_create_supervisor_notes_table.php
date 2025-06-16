<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('supervisor_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shift_log_id')->constrained('shift_logs')->cascadeOnDelete();
            $table->longText('note');
            $table->enum('note_type', ['day_shift', 'night_shift'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supervisor_notes');
    }
};
