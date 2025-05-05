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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->string('model')->comment('Model mobil');
            $table->string('license_plate')->comment('Nomor plat');
            $table->string('year')->nullable()->comment('Tahun pembuatan');
            $table->string('color')->nullable()->comment('Warna');
            $table->string('vin')->nullable()->comment('Vehicle Identification Number');
            $table->string('engine_number')->nullable()->comment('Nomor mesin');
            $table->string('transmission')->nullable()->comment('Jenis transmisi');
            $table->string('fuel_type')->nullable()->comment('Jenis bahan bakar');
            $table->text('notes')->nullable()->comment('Catatan tambahan');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Unique constraint untuk nomor plat
            $table->unique('license_plate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
