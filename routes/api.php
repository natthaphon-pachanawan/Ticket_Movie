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
use App\Http\Controllers\TicketController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\SlipController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Movies
Route::get('/movies/list', [MovieController::class, 'index']);
Route::get('/movies/detail/{id}', [MovieController::class, 'show']);

// Cinemas
Route::get('/cinemas/list', [CinemaController::class, 'index']);
Route::get('/cinemas/detail/{id}', [CinemaController::class, 'show']);

// Roles
Route::get('/roles/list', [RoleController::class, 'index']);
Route::get('/roles/detail/{id}', [RoleController::class, 'show']);

// Auth (public)
Route::post('/auth/register', [UserController::class, 'register']);
Route::post('/auth/login', [UserController::class, 'login']);

// Screening Rooms - List & Detail (public)
Route::get('/screening-rooms/list', [ScreeningRoomController::class, 'index']);
Route::get('/screening-rooms/detail/{id}', [ScreeningRoomController::class, 'show']);

// Seats - List (สาขาขึ้นกับห้องฉาย)
Route::get('/seats/list/{screening_room_id}', [SeatController::class, 'index']);

// Screenings - List & Detail
Route::get('/screenings/list', [ScreeningController::class, 'index']);
Route::get('/screenings/detail/{id}', [ScreeningController::class, 'show']);

Route::middleware('auth:api')->group(function () {
    // Auth
    Route::post('/auth/logout', [UserController::class, 'logout']);

    // Movies - Create, Update, Delete (Admin หรือผู้ที่มีสิทธิ์)
    Route::post('/movies/create', [MovieController::class, 'store']);
    Route::post('/movies/update/{id}', [MovieController::class, 'update']);
    Route::delete('/movies/delete/{id}', [MovieController::class, 'destroy']);

    // Cinemas - Create, Update, Delete
    Route::post('/cinemas/create', [CinemaController::class, 'store']);
    Route::post('/cinemas/update/{id}', [CinemaController::class, 'update']);
    Route::delete('/cinemas/delete/{id}', [CinemaController::class, 'destroy']);

    // Roles - Create, Update, Delete (สำหรับ Admin)
    Route::post('/roles/create', [RoleController::class, 'store']);
    Route::post('/roles/update/{id}', [RoleController::class, 'update']);
    Route::delete('/roles/delete/{id}', [RoleController::class, 'destroy']);

    // Screening Rooms - Create, Update, Delete
    Route::post('/screening-rooms/create', [ScreeningRoomController::class, 'store']);
    Route::post('/screening-rooms/update/{id}', [ScreeningRoomController::class, 'update']);
    Route::delete('/screening-rooms/delete/{id}', [ScreeningRoomController::class, 'destroy']);

    // Seats - Create, Update, Delete
    Route::post('/seats/create', [SeatController::class, 'store']);
    Route::post('/seats/update/{id}', [SeatController::class, 'update']);
    Route::delete('/seats/delete/{id}', [SeatController::class, 'destroy']);

    // Screenings - Create, Update, Delete
    Route::post('/screenings/create', [ScreeningController::class, 'store']);
    Route::post('/screenings/update/{id}', [ScreeningController::class, 'update']);
    Route::delete('/screenings/delete/{id}', [ScreeningController::class, 'destroy']);

    // Bookings - (ข้อมูลส่วนตัวของผู้ใช้ จึงควรต้อง auth)
    Route::get('/bookings/list', [BookingController::class, 'index']);
    Route::get('/bookings/detail/{id}', [BookingController::class, 'show']);
    Route::post('/bookings/create', [BookingController::class, 'store']);
    Route::post('/bookings/update/{id}', [BookingController::class, 'update']);
    Route::delete('/bookings/delete/{id}', [BookingController::class, 'destroy']);

    // Tickets - (เป็นข้อมูลที่เกี่ยวกับการจองตั๋วส่วนตัว)
    Route::get('/tickets/list', [TicketController::class, 'index']);
    Route::get('/tickets/detail/{id}', [TicketController::class, 'show']);
    Route::post('/tickets/create', [TicketController::class, 'store']);
    Route::post('/tickets/update/{id}', [TicketController::class, 'update']);
    Route::delete('/tickets/delete/{id}', [TicketController::class, 'destroy']);

    // Slips - (ข้อมูลการชำระเงินและสลิปเป็นข้อมูลส่วนตัว)
    Route::get('/slips/list', [SlipController::class, 'index']);
    Route::get('/slips/detail/{id}', [SlipController::class, 'show']);
    Route::post('/slips/create', [SlipController::class, 'store']);
    Route::post('/slips/update/{id}', [SlipController::class, 'update']);
    Route::delete('/slips/delete/{id}', [SlipController::class, 'destroy']);
    
});
