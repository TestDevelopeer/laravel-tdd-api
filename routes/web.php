<?php

use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/posts', [PostController::class, 'store']);
Route::patch('/posts/{post}', [PostController::class, 'update']);

