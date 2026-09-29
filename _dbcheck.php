<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$article = DB::table('articles')->first();
echo 'sample image: ' . $article->image . PHP_EOL;
echo 'sample cover: ' . DB::table('users')->where('role', 'author')->value('cover_image') . PHP_EOL;
