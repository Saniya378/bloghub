<?php

use App\Http\Controllers\Api\PostController;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function () {
    Route::get('/posts', [PostController::class, 'index'])->name('api.posts.index');
});
