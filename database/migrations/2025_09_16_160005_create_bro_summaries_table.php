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
        Schema::create('bro_summaries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('nominee_id');
            $table->integer('final_score')->nullable();
            $table->integer('bro_total')->nullable();
            $table->integer('bro_a')->nullable();
            $table->integer('bro_b')->nullable();
            $table->integer('bro_c')->nullable();
            $table->integer('bro_d')->nullable();
            $table->integer('bro_e')->nullable();
            $table->integer('ex1_total')->nullable();
            $table->integer('ex1_a')->nullable();
            $table->integer('ex1_b')->nullable();
            $table->integer('ex1_c')->nullable();
            $table->integer('ex1_d')->nullable(); 
            $table->integer('ex1_e')->nullable();
            $table->integer('ex2_total')->nullable();
            $table->integer('ex2_a')->nullable();
            $table->integer('ex2_b')->nullable();
            $table->integer('ex2_c')->nullable();
            $table->integer('ex2_d')->nullable();
            $table->integer('ex2_e')->nullable();
            $table->integer('ex3_total')->nullable();
            $table->integer('ex3_a')->nullable();
            $table->integer('ex3_b')->nullable();
            $table->integer('ex3_c')->nullable();
            $table->integer('ex3_d')->nullable();
            $table->integer('ex3_e')->nullable();  
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bro_summaries');
    }
};
