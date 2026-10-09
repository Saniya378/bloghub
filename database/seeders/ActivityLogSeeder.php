<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class ActivityLogSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::first();
        $articles = Article::with('user')->take(10)->get();

        if (ActivityLog::count() === 0 && $admin) {
            ActivityLog::create([
                'user_id' => $admin->id,
                'action' => 'Approved',
                'module' => 'Author',
                'description' => 'Admin approved author Elena Rostova account',
                'ip_address' => '127.0.0.1',
                'created_at' => now()->subDays(6),
                'updated_at' => now()->subDays(6),
            ]);

            ActivityLog::create([
                'user_id' => $admin->id,
                'action' => 'Approved',
                'module' => 'Author',
                'description' => 'Admin approved author Marcus Chen account',
                'ip_address' => '127.0.0.1',
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ]);

            foreach ($articles as $i => $art) {
                $author = $art->user ?? $admin;
                ActivityLog::create([
                    'user_id' => $author->id,
                    'action' => 'Submitted',
                    'module' => 'Article',
                    'description' => "Author '{$author->name}' submitted article '{$art->title}'",
                    'subject_type' => Article::class,
                    'subject_id' => $art->id,
                    'ip_address' => '127.0.0.1',
                    'created_at' => now()->subDays(7 - ($i % 6))->subHours(4),
                    'updated_at' => now()->subDays(7 - ($i % 6))->subHours(4),
                ]);

                ActivityLog::create([
                    'user_id' => $admin->id,
                    'action' => $art->status === 'rejected' ? 'Rejected' : 'Approved',
                    'module' => 'Article',
                    'description' => "Admin " . ($art->status === 'rejected' ? 'rejected' : 'approved') . " article '{$art->title}'",
                    'subject_type' => Article::class,
                    'subject_id' => $art->id,
                    'ip_address' => '127.0.0.1',
                    'created_at' => now()->subDays(7 - ($i % 6)),
                    'updated_at' => now()->subDays(7 - ($i % 6)),
                ]);
            }

            ActivityLog::create([
                'user_id' => $admin->id,
                'action' => 'Updated',
                'module' => 'Category',
                'description' => 'Admin updated category Technology description',
                'ip_address' => '127.0.0.1',
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ]);

            ActivityLog::create([
                'user_id' => $admin->id,
                'action' => 'Activated',
                'module' => 'User',
                'description' => 'Admin activated reader account',
                'ip_address' => '127.0.0.1',
                'created_at' => now()->subDay(),
                'updated_at' => now()->subDay(),
            ]);
        }
    }
}
