<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('frontend.home');
});

Route::get('/categories', function () {
    return view('frontend.categories');
});

Route::get('/trending', function () {
    return view('frontend.trending');
});

Route::get('/about', function () {
    return view('frontend.about');
});

Route::get('/contact', function () {
    return view('frontend.contact');
});

Route::get('/login', function () {
    return view('frontend.login');
});

Route::get('/loginnew', function () {
    return view('frontend.login');
});


Route::get('/register', function () {
    return view('frontend.register');
});

Route::get('/forget-password', function () {
    return view('frontend.forget-password');
});

Route::get('/blog', function () {
    return view('frontend.blog-single');
});

Route::get('/search', function () {
    return view('frontend.search-results');
});

Route::get('/author', function () {
    return view('frontend.author');
});

Route::get('/tag', function () {
    return view('frontend.tag');
});

Route::get('/newsletter', function () {
    return view('frontend.newsletter');
});

Route::get('/404', function () {
    return view('frontend.404');
});

Route::get('/terms', function () {
    return view('frontend.terms');
});

Route::get('/privacy', function () {
    return view('frontend.privacy');
});


Route::get('/dashboard', function () {
    return view('backend.index');
});

Route::get('/docs', function () {
    return view('backend.docs');
});

Route::get('/orders', function () {
    return view('backend.orders');
});

Route::get('/notifications', function () {
    return view('backend.notifications');
});

Route::get('/account', function () {
    return view('backend.account');
});

Route::get('/settings', function () {
    return view('backend.settings');
});

Route::get('/charts', function () {
    return view('backend.charts');
});

Route::get('/help', function () {
    return view('backend.help');
});

Route::get('/login', function () {
    return view('backend.login');
});

Route::get('/signup', function () {
    return view('backend.signup');
});

Route::get('/reset-password', function () {
    return view('backend.reset-password');
});


Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
