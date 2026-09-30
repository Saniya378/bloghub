<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$articles = \App\Models\Article::published()->select('id','title','slug')->take(5)->get();
foreach ($articles as $a) {
    echo $a->slug . PHP_EOL;
}
