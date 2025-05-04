<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\Gallery;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('galleries', function (Blueprint $table) {
            $table->string('slug')->after('title')->nullable();
        });

        // Generate slugs for existing galleries
        $galleries = Gallery::all();
        foreach ($galleries as $gallery) {
            $gallery->slug = Str::slug($gallery->title);
            $gallery->save();
        }

        // Make slug required after populating existing records
        Schema::table('galleries', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('galleries', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
