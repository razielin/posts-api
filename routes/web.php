<?php

use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'api',
], function () {
    Route::get('/posts', [PostController::class, 'allPosts']);
    Route::get('/posts/{id}', [PostController::class, 'getPost']);
    Route::post('/posts', [PostController::class, 'createPost']);
    Route::put('/posts/{id}', [PostController::class, 'editPost']);

    Route::get('/token', function () {
        return csrf_token();
    });
});
