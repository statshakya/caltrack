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
        Schema::create('food_entries', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID (1, 2, 3...)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // This creates a column 'user_id' that links to 'users' table
            // onDelete('cascade') means if user is deleted, their food entries are also deleted
            
            $table->string('food_name'); // Text field for food name
            $table->integer('calories'); // Number field for calories
            $table->date('entry_date'); // Date when they ate it
            
            $table->timestamps(); // Creates 'created_at' and 'updated_at' columns
        });
        
        // Create an index for faster queries
        Schema::table('food_entries', function (Blueprint $table) {
            $table->index(['user_id', 'entry_date']);
            // This makes searching by user_id AND date much faster
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food_entries');
    }
};