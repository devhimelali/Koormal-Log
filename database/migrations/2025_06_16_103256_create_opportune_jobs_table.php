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
        Schema::create('opportune_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('wo_number')->nullable();
            $table->string('asset_no')->nullable();
            $table->longText('asset_description')->nullable();
            $table->longText('work_description')->nullable();
            $table->string('status')->nullable();
            $table->string('due_start')->nullable();
            $table->string('job_type')->nullable();
            $table->string('priority')->nullable();
            $table->string('raised')->nullable();
            $table->string('start_date')->nullable();
            $table->string('duration')->nullable();
            $table->string('department')->nullable();
            $table->string('material_cost')->nullable();
            $table->string('other_cost')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opportune_jobs');
    }
};
