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
        Schema::table('mechanic_service', function (Blueprint $table) {
            $table->decimal('labor_cost', 10, 2)->default(0)->after('notes');
            $table->date('week_start')->nullable()->after('labor_cost');
            $table->date('week_end')->nullable()->after('week_start');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mechanic_service', function (Blueprint $table) {
            $table->dropColumn(['labor_cost', 'week_start', 'week_end']);
        });
    }
};
