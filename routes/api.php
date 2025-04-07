<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\CinemaController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Movies
Route::get('/movies/list', [MovieController::class, 'index']);
Route::get('/movies/detail/{id}', [MovieController::class, 'show']);
Route::post('/movies/create', [MovieController::class, 'store']);
Route::post('/movies/update/{id}', [MovieController::class, 'update']);
Route::delete('/movies/delete/{id}', [MovieController::class, 'destroy']);

// Cinemas
Route::get('/cinemas/list', [CinemaController::class, 'index']);
Route::get('/cinemas/detail/{id}', [CinemaController::class, 'show']);
Route::post('/cinemas/create', [CinemaController::class, 'store']);
Route::post('/cinemas/update/{id}', [CinemaController::class, 'update']);
Route::delete('/cinemas/delete/{id}', [CinemaController::class, 'destroy']);
