<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calorie_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('entry_date');
            
            // Targets for the day
            $table->integer('calorie_target')->nullable();
            $table->integer('protein_target')->nullable();
            $table->integer('carbs_target')->nullable();
            $table->integer('fat_target')->nullable();
            
            // Actual consumption
            $table->decimal('calories_consumed', 8, 2)->default(0);
            $table->decimal('protein_consumed', 8, 2)->default(0);
            $table->decimal('carbs_consumed', 8, 2)->default(0);
            $table->decimal('fat_consumed', 8, 2)->default(0);
            
            // Water intake
            $table->integer('water_ml')->default(0);
            
            // Exercise
            $table->integer('exercise_minutes')->default(0);
            $table->integer('calories_burned')->default(0);
            
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Unique constraint to prevent duplicate daily entries
            $table->unique(['user_id', 'entry_date']);
        });

        // Create indexes
        Schema::table('calorie_entries', function (Blueprint $table) {
            $table->index(['user_id', 'entry_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calorie_entries');
    }
};