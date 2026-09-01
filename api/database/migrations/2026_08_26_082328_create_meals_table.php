<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->enum('meal_type', [
                'breakfast',
                'morning_snack',
                'lunch',
                'afternoon_snack',
                'dinner',
                'evening_snack',
                'other'
            ]);
            $table->date('meal_date');
            $table->time('meal_time')->nullable();
            $table->text('notes')->nullable();
            
            // Nutritional totals (calculated)
            $table->decimal('total_calories', 8, 2)->default(0);
            $table->decimal('total_protein', 8, 2)->default(0);
            $table->decimal('total_carbs', 8, 2)->default(0);
            $table->decimal('total_fat', 8, 2)->default(0);
            
            $table->timestamps();
            $table->softDeletes();
        });

        // Create indexes
        Schema::table('meals', function (Blueprint $table) {
            $table->index(['user_id', 'meal_date']);
            $table->index('meal_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meals');
    }
};