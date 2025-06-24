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
        Schema::create('work_order_moves', function (Blueprint $table) {
            $table->id();
            $table->string('wo_number');
            $table->string('from_date');
            $table->string('from_shift');
            $table->string('to_date');
            $table->string('to_shift');
            $table->longText('reason');
            $table->string('moved_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_order_moves');
    }
};
