<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/github', [App\Http\Controllers\GitHubController::class, 'index']);
