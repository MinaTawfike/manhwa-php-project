<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Category-Comic Pivot Table Migration
 * 
 * Creates the many-to-many relationship table between categories and comics.
 * 
 * Features:
 * - Foreign keys with cascade delete for data integrity
 * - Unique composite key to prevent duplicate assignments
 * - Proper indexes for performance
 * - Timestamps for tracking when relationships were created
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
        Schema::create('category_comic', function (Blueprint $table) {
            $table->foreignId('comic_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            // Unique composite key to prevent duplicate category assignments
            $table->unique(['comic_id', 'category_id']);
            
            // Indexes for faster queries
            $table->index('comic_id');
            $table->index('category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_comic');
    }
};
