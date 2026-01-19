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
        Schema::create('chapters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comic_id')->constrained()->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->integer('number');
            $table->integer('rating')->default(0);
            $table->text('comment')->nullable();
            $table->timestamps();
        });

        Schema::create('chapter_user_bookmarks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chapter_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['chapter_id', 'user_id']);
        });

        Schema::create('chapter_user_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chapter_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('rating')->default(0);
            $table->timestamps();
            $table->unique(['chapter_id', 'user_id']);
        });

        Schema::create('chapter_images', function (Blueprint $table) {
            $table->id();

            // Link each image to a chapter; delete images if chapter is deleted
            $table->foreignId('chapter_id')
                ->constrained()
                ->cascadeOnDelete(); // Delete images when chapter is deleted

            // Store relative path on the 'public' disk, e.g. "chapter_pages/123/filename.jpg"
            $table->string('path'); // relative path e.g. chapter_pages/{chapter_id}/xxx.jpg

            // 1-based page number for ordering within a chapter
            $table->unsignedInteger('page_number'); // 1-based ordering

            // Optional alt text/caption
            $table->string('alt')->nullable(); // optional alt text/caption
            $table->timestamps();

            $table->unique(['chapter_id', 'page_number']); //  Ensure unique page numbers per chapter and help sorting
            $table->index(['chapter_id', 'page_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chapter_user_ratings');
        Schema::dropIfExists('chapter_user_bookmarks');
        Schema::dropIfExists('chapters');
        Schema::dropIfExists('chapter_images');
    }
};
