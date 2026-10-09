<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // published_at was a DATE column, so the time component was discarded
        // on write. That made a post published at 10:05 render as "10 hrs ago".
        Schema::table('articles', function (Blueprint $table) {
            $table->dateTime('published_at')->nullable()->change();
        });

        // Backfill today's rows so they do not read as midnight. The original publish
        // time is gone, so the post's created_at time-of-day is the best
        // available approximation. Older rows keep 00:00:00.
        // Done in PHP so the migration runs on MySQL and SQLite alike.
        DB::table('articles')
            ->whereNotNull('published_at')
            ->whereDate('published_at', now()->toDateString())
            ->get(['id', 'published_at', 'created_at'])
            ->each(function ($row) {
                if (substr((string) $row->published_at, 11, 8) !== '00:00:00') {
                    return;
                }

                $time = substr((string) $row->created_at, 11, 8);

                // Never invent a publish time that is still in the future.
                if ($time === '' || $time > now()->format('H:i:s')) {
                    $time = now()->format('H:i:s');
                }

                DB::table('articles')
                    ->where('id', $row->id)
                    ->update(['published_at' => substr((string) $row->published_at, 0, 10) . ' ' . $time]);
            });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->date('published_at')->nullable()->change();
        });
    }
};