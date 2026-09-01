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
        Schema::table('food_entries', function (Blueprint $table) {
            $table->decimal('protein',8,2)->nullable()->after('calories');
            $table->decimal('carbohydrates', 8, 2)->nullable()->after('protein');
            $table->decimal('fat', 8, 2)->nullable()->after('carbohydrates');
            $table->decimal('fiber', 8, 2)->nullable()->after('fat');
            $table->decimal('sugar', 8, 2)->nullable()->after('fiber');


            // Add serving size info
            $table->decimal('serving_size', 8, 2)->default(100)->after('sugar');
            $table->string('serving_unit')->default('g')->after('serving_size');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('food_entries', function (Blueprint $table) {
                   $table->dropColumn([
                'protein',
                'carbohydrates',
                'fat',
                'fiber',
                'sugar',
                'serving_size',
                'serving_unit',
            ]);
        });
    }
};
