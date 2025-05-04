<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\Promo;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('promos', function (Blueprint $table) {
            $table->string('slug')->after('title')->nullable();
        });

        // Generate slugs for existing promos
        $promos = Promo::all();
        foreach ($promos as $promo) {
            $promo->slug = Str::slug($promo->title);
            $promo->save();
        }

        // Make slug required after populating existing records
        Schema::table('promos', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('promos', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
