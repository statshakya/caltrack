<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('food_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('brand')->nullable();
            $table->text('description')->nullable();
            
            // Serving information
            $table->decimal('serving_size', 8, 2); // In grams
            $table->string('serving_unit')->default('g');
            
            // Nutritional information per serving
            $table->decimal('calories', 8, 2);
            $table->decimal('protein', 8, 2)->default(0);
            $table->decimal('carbohydrates', 8, 2)->default(0);
            $table->decimal('fat', 8, 2)->default(0);
            $table->decimal('fiber', 8, 2)->default(0);
            $table->decimal('sugar', 8, 2)->default(0);
            $table->decimal('sodium', 8, 2)->default(0);
            $table->decimal('cholesterol', 8, 2)->default(0);
            
            // Metadata
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_custom')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('barcode')->nullable()->unique();
            
            $table->timestamps();
            $table->softDeletes();
        });

        // Create indexes
        Schema::table('food_items', function (Blueprint $table) {
            $table->index('name');
            $table->index('barcode');
            $table->index('is_verified');
            $table->fullText(['name', 'brand', 'description']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('food_items');
    }
};