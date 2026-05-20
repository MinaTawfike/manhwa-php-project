<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Categories System Migration
 * 
 * Creates the categories table for organizing comics into genres/categories.
 * 
 * Features:
 * - Unique slug for SEO-friendly URLs
 * - Description field for category details
 * - Proper indexes for performance
 * - Timestamps for tracking
 * 
 * @package Database\Migrations
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
            
            // Index for faster slug lookups
            $table->index('slug');
            // Index for name searches
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
