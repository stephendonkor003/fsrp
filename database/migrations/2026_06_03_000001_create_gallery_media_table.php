<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attp_gallery_media', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('media_type', 20)->index();
            $table->string('category', 80)->default('events')->index();
            $table->text('description')->nullable();
            $table->string('caption', 500)->nullable();
            $table->string('alt_text', 255)->nullable();
            $table->string('file_path');
            $table->string('thumbnail_path')->nullable();
            $table->string('file_name');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size_bytes')->nullable();
            $table->string('status', 30)->default('draft')->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->date('captured_at')->nullable();
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('submitted_at')->nullable();
            $table->foreignUuid('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('published_at')->nullable()->index();
            $table->text('review_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attp_gallery_media');
    }
};
