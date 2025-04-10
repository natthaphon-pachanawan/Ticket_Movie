<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\CinemaController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ScreeningRoomController;
use App\Http\Controllers\SeatController;
use App\Http\Controllers\ScreeningController;

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

//Screening Room
Route::get('/screening-rooms/list', [ScreeningRoomController::class, 'index']);
Route::get('/screening-rooms/detail/{id}', [ScreeningRoomController::class, 'show']);
Route::post('/screening-rooms/create', [ScreeningRoomController::class, 'store']);
Route::post('/screening-rooms/update/{id}', [ScreeningRoomController::class, 'update']);
Route::delete('/screening-rooms/delete/{id}', [ScreeningRoomController::class, 'destroy']);

// Seats
Route::get('/seats/list/{screening_room_id}', [SeatController::class, 'index']);
Route::post('/seats/create', [SeatController::class, 'store']);
Route::post('/seats/update/{id}', [SeatController::class, 'update']);
Route::delete('/seats/delete/{id}', [SeatController::class, 'destroy']);

// Screening
Route::get('/screenings/list', [ScreeningController::class, 'index']);
Route::get('/screenings/detail/{id}', [ScreeningController::class, 'show']);
Route::post('/screenings/create', [ScreeningController::class, 'store']);
Route::post('/screenings/update/{id}', [ScreeningController::class, 'update']);
Route::delete('/screenings/delete/{id}', [ScreeningController::class, 'destroy']);

Route::middleware('auth:api')->group(function () {
    // Auth
    Route::post('/auth/logout', [UserController::class, 'logout']);
});
