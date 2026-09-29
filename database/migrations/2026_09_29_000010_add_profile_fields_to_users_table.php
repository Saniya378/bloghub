<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar')->nullable();
            $table->string('cover_image')->nullable();
            $table->text('bio')->nullable();
            $table->string('tagline')->nullable();
            $table->string('specialty')->nullable();
            $table->json('social')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['avatar', 'cover_image', 'bio', 'tagline', 'specialty', 'social']);
        });
    }
};
