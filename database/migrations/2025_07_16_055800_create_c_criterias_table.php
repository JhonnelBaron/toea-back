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
        Schema::create('c_criterias', function (Blueprint $table) {
            $table->id();
            $table->string('number')->nullable();
            $table->text('title')->nullable();
            $table->longText('description')->nullable();
            $table->longText('means_of_verification')->nullable();
            $table->enum('criteria_function', ['criteria', 'header', 'sub-header'])->nullable();
            $table->json('designated_offices')->nullable();
            $table->boolean('bro_small')->default(false);
            $table->boolean('bro_medium')->default(false);
            $table->boolean('bro_large')->default(false);
            $table->boolean('gp_small')->default(false);
            $table->boolean('gp_medium')->default(false);
            $table->boolean('gp_large')->default(false);
            $table->boolean('bti_rtcstc')->default(false);
            $table->boolean('bti_ptcdtc')->default(false);
            $table->boolean('bti_tas')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('c_criterias');
    }
};
