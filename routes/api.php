<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\CinemaController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;

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

// Roles
Route::get('/roles/list', [RoleController::class, 'index']);
Route::get('/roles/detail/{id}', [RoleController::class, 'show']);
Route::post('/roles/create', [RoleController::class, 'store']);
Route::post('/roles/update/{id}', [RoleController::class, 'update']);
Route::delete('/roles/delete/{id}', [RoleController::class, 'destroy']);

// Auth
Route::post('/auth/register', [UserController::class, 'register']);
Route::post('/auth/login', [UserController::class, 'login']);


Route::middleware('auth:api')->group(function () {
    // Auth
    Route::post('/auth/logout', [UserController::class, 'logout']);
});
