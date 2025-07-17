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
        Schema::create('e_requirements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('e_criteria_id');
            $table->text('requirement_description')->nullable();
            $table->string('point_value')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('e_requirements');
    }
};
