<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt');
            $table->longText('content');
            $table->string('category_slug');
            $table->json('tags')->nullable();
            $table->string('image')->nullable();
            $table->string('status')->default('draft'); // draft, published
            $table->integer('views')->default(0);
            $table->integer('likes')->default(0);
            $table->integer('comment_count')->default(0);
            $table->string('reading_time')->nullable();
            $table->date('published_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('category_slug');
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
