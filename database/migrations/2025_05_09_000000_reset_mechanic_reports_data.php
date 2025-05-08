<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Truncate mechanic_reports table
        DB::table('mechanic_reports')->truncate();

        // Reset labor_cost in mechanic_service table
        DB::table('mechanic_service')
            ->update([
                'labor_cost' => 0,
                'week_start' => null,
                'week_end' => null
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cannot restore deleted data
    }
};
