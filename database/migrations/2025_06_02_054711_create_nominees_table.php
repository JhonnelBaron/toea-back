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
        Schema::create('nominees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->enum('nominee_type', ['BRO', 'GP', 'BTI'])->nullable();
            $table->enum('nominee_category', ['small', 'medium', 'large', 'ptc-dtc', 'rtc-stc', 'tas'])->nullable();
            $table->string('region')->nullable();
            $table->string('province')->nullable();
            $table->text('nominee_name')->nullable();
            $table->string('status')->nullable(); // Added status field with default value 'pending'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nominees');
    }
};
