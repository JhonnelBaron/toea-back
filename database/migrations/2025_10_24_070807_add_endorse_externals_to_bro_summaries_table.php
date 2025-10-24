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
        Schema::table('bro_summaries', function (Blueprint $table) {
            $table->boolean('endorse_externals')->default(false)->after('nominee_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bro_summaries', function (Blueprint $table) {
            $table->dropColumn('endorse_externals');
        });
    }
};
