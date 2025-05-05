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
        Schema::create('mechanic_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mechanic_id')->constrained()->onDelete('cascade');
            $table->date('week_start');
            $table->date('week_end');
            $table->integer('services_count')->default(0);
            $table->decimal('total_labor_cost', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->boolean('is_paid')->default(false);
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            // Unique constraint to prevent duplicate reports for the same mechanic and week
            $table->unique(['mechanic_id', 'week_start', 'week_end']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mechanic_reports');
    }
};
