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

/*
|--------------------------------------------------------------------------
| Public Routes (ไม่ต้องล็อกอิน)
|--------------------------------------------------------------------------
*/

Route::get('/movies/list', [MovieController::class, 'index']);
Route::get('/movies/detail/{id}', [MovieController::class, 'show']);

Route::get('/cinemas/list', [CinemaController::class, 'index']);
Route::get('/cinemas/detail/{id}', [CinemaController::class, 'show']);

Route::get('/roles/list', [RoleController::class, 'index']);
Route::get('/roles/detail/{id}', [RoleController::class, 'show']);

Route::post('/auth/register', [UserController::class, 'register']);
Route::post('/auth/login',    [UserController::class, 'login']);

Route::get('/screening-rooms/list',           [ScreeningRoomController::class, 'index']);
Route::get('/screening-rooms/detail/{id}',    [ScreeningRoomController::class, 'show']);

Route::get('/seats/list/{screening_id}', [SeatController::class, 'index']);

Route::get('/screenings/list',                           [ScreeningController::class, 'index']);
Route::get('/screenings/detail/{id}',                    [ScreeningController::class, 'show']);
Route::get('/screenings/filter/by-movie',                [ScreeningController::class, 'listByMovie']);
Route::get('/screenings/filter/by-movie-and-date',       [ScreeningController::class, 'filterByMovieAndDate']);


/*
|--------------------------------------------------------------------------
| Authenticated User Routes (ล็อกอินแล้ว — ไม่ต้องเป็น Admin)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:api')->group(function () {
    // Logout / Profile
    Route::post('/auth/logout',            [UserController::class, 'logout']);
    Route::get('/user/profile',           [UserController::class, 'profile']);
    Route::post('/user/profile/update',    [UserController::class, 'updateProfile']);

    // จองตั๋ว (Bookings)
    Route::get('/bookings/list',          [BookingController::class, 'index']);
    Route::get('/bookings/detail/{id}',   [BookingController::class, 'show']);
    Route::post('/bookings/create',        [BookingController::class, 'store']);
    Route::post('/bookings/update/{id}',   [BookingController::class, 'update']);
    Route::delete('/bookings/delete/{id}', [BookingController::class, 'destroy']);
    Route::post('/bookings/cancel/{id}', [BookingController::class, 'cancel']);

    // อัปโหลดสลิป (Slips)
    Route::post('/slips/create',           [SlipController::class, 'store']);
    Route::get('/slips/detail/{id}',      [SlipController::class, 'show']);

    // ดูตั๋วของตัวเอง
    Route::get('/tickets/my',             [TicketController::class, 'myTickets']);
});


/*
|--------------------------------------------------------------------------
| Admin‑Only Routes (ล็อกอิน + ต้องเป็น Admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:api', 'is_admin'])->group(function () {
    // Movies
    Route::post('/movies/create',        [MovieController::class, 'store']);
    Route::post('/movies/update/{id}',   [MovieController::class, 'update']);
    Route::delete('/movies/delete/{id}',   [MovieController::class, 'destroy']);

    // Cinemas
    Route::post('/cinemas/create',       [CinemaController::class, 'store']);
    Route::post('/cinemas/update/{id}',  [CinemaController::class, 'update']);
    Route::delete('/cinemas/delete/{id}',  [CinemaController::class, 'destroy']);

    // Roles
    Route::post('/roles/create',         [RoleController::class, 'store']);
    Route::post('/roles/update/{id}',    [RoleController::class, 'update']);
    Route::delete('/roles/delete/{id}',    [RoleController::class, 'destroy']);

    // Screening Rooms
    Route::post('/screening-rooms/create',      [ScreeningRoomController::class, 'store']);
    Route::post('/screening-rooms/update/{id}', [ScreeningRoomController::class, 'update']);
    Route::delete('/screening-rooms/delete/{id}', [ScreeningRoomController::class, 'destroy']);

    // Seats
    Route::post('/seats/create',        [SeatController::class, 'store']);
    Route::post('/seats/update/{id}',   [SeatController::class, 'update']);
    Route::delete('/seats/delete/{id}',   [SeatController::class, 'destroy']);

    // Screenings
    Route::post('/screenings/create',        [ScreeningController::class, 'store']);
    Route::post('/screenings/update/{id}',   [ScreeningController::class, 'update']);
    Route::delete('/screenings/delete/{id}',   [ScreeningController::class, 'destroy']);

    // Full Tickets management
    Route::get('/tickets/list',        [TicketController::class, 'index']);
    Route::get('/tickets/detail/{id}', [TicketController::class, 'show']);
    Route::post('/tickets/create',      [TicketController::class, 'store']);
    Route::post('/tickets/update/{id}', [TicketController::class, 'update']);
    Route::delete('/tickets/delete/{id}', [TicketController::class, 'destroy']);

    // Review & approve slips
    Route::get('/slips/list',          [SlipController::class, 'index']);
    Route::post('/slips/update/{id}',   [SlipController::class, 'update']);
    Route::delete('/slips/delete/{id}',   [SlipController::class, 'destroy']);
});
