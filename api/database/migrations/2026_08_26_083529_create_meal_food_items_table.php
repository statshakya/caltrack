<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meal_food_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meal_id')->constrained()->onDelete('cascade');
            $table->foreignId('food_item_id')->constrained()->onDelete('cascade');
            $table->decimal('quantity', 8, 2); // Number of servings
            $table->string('unit')->default('serving');
            
            // Nutritional values for this specific quantity
            $table->decimal('calories', 8, 2);
            $table->decimal('protein', 8, 2)->default(0);
            $table->decimal('carbohydrates', 8, 2)->default(0);
            $table->decimal('fat', 8, 2)->default(0);
            
            $table->timestamps();
        });

        // Create indexes
        Schema::table('meal_food_items', function (Blueprint $table) {
            $table->index(['meal_id', 'food_item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meal_food_items');
    }
};