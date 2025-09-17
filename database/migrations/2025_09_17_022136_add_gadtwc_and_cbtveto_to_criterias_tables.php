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
        $tables = [
            'a_criterias',
            'b_criterias',
            'c_criterias',
            'd_criterias',
            'e_criterias',
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->boolean('gadtwc')->default(false)->after('ws');
                $table->boolean('cbtveto')->default(false)->after('gadtwc');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'a_criterias',
            'b_criterias',
            'c_criterias',
            'd_criterias',
            'e_criterias',
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn(['gadtwc', 'cbtveto']);
            });
        }
    }
};
