<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('article_id')->constrained()->cascadeOnDelete();
            $table->text('content');
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->integer('likes')->default(0);
            $table->timestamps();

            $table->index('status');
            $table->index('article_id');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
