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
        Schema::create('shift_logs', function (Blueprint $table) {
            $table->id();
            $table->string('shift_name')->nullable();
            $table->string('wo_number')->nullable();
            $table->string('asset_no')->nullable();
            $table->longText('asset_description')->nullable();
            $table->longText('work_description')->nullable();
            $table->string('labour')->nullable();
            $table->string('duration')->nullable();
            $table->text('trades')->nullable();
            $table->string('due_start')->nullable();
            $table->string('status')->nullable();
            $table->string('raised')->nullable();
            $table->string('start_date')->nullable();
            $table->string('priority')->nullable();
            $table->string('job_type')->nullable();
            $table->string('department')->nullable();
            $table->string('material_cost')->nullable();
            $table->string('labor_cost')->nullable();
            $table->string('other_cost')->nullable();
            $table->boolean('is_excel_upload')->default(0);
            $table->unsignedInteger('position')->nullable();
            $table->longText('supervisor_notes')->nullable();
            $table->boolean('mark_as_complete')->default(0);
            $table->string('progress')->default('0');
            $table->longText('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shift_logs');
    }
};
