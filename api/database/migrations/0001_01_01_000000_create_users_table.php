<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('phone')->nullable();
            
            // Physical attributes
            $table->decimal('height_cm', 5, 2)->nullable(); // Height in centimeters
            $table->decimal('weight_kg', 5, 2)->nullable(); // Weight in kilograms
            $table->integer('age')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            
            // Fitness attributes
            $table->enum('activity_level', [
                'sedentary',
                'lightly_active',
                'moderately_active',
                'very_active',
                'extremely_active'
            ])->default('sedentary');
            
            $table->enum('goal', [
                'lose_weight',
                'maintain_weight',
                'gain_weight',
                'build_muscle'
            ])->default('maintain_weight');
            
            // Calculated fields
            $table->integer('daily_calorie_target')->nullable();
            $table->integer('daily_protein_target')->nullable();
            $table->integer('daily_carbs_target')->nullable();
            $table->integer('daily_fat_target')->nullable();
            
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        // Create indexes
        Schema::table('users', function (Blueprint $table) {
            $table->index('email');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};