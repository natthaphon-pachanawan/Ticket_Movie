<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Seat;
use Illuminate\Support\Facades\DB;

class SeatController extends Controller
{
    //  App\Http\Controllers\SeatController.php
    public function index($screening_room_id)
    {
        // id ของ screening ที่ front‑end ส่งมา (เช่น 12)
        $screeningId = request()->query('screening_id');

        $seats = DB::table('seats')
            ->select('seats.*')
            ->selectRaw(
                "EXISTS(
                SELECT 1
                FROM booking_seats bs
                JOIN bookings b ON b.id = bs.booking_id
                WHERE bs.seat_id     = seats.id
                  AND b.screening_id = ?
                  AND b.status       = 'active'
            ) AS is_reserved",
                [$screeningId]          // <‑‑ binding
            )
            ->where('seats.screening_room_id', $screening_room_id)
            ->get();

        // ได้ผลลัพธ์เป็น  [{… , "is_reserved":1}, { …,"is_reserved":0}, … ]
        return $this->returnJson($seats);
    }

    public function store(Request $request)
    {
        $request->validate([
            'screening_room_id' => 'required|exists:screening_rooms,id',
            'seat_number' => 'required|string|max:255',
            'row' => 'required|integer|min:0',
            'column' => 'required|integer|min:0',
            'seat_type' => 'required|string|max:255',
            'is_active' => 'required|boolean',
        ]);

        $seat = Seat::create([
            'screening_room_id' => $request->screening_room_id,
            'seat_number' => $request->seat_number,
            'row' => $request->row,
            'column' => $request->column,
            'seat_type' => $request->seat_type,
            'is_active' => $request->is_active,
        ]);
        if (!$seat) {
            return $this->returnError('เพิ่มข้อมูลไม่สำเร็จ', 500);
        }

        $seatIds = [$seat->id];

        $exists = DB::table('booking_seats')
            ->join('bookings', 'bookings.id', '=', 'booking_seats.booking_id')
            ->whereIn('booking_seats.seat_id', $seatIds)
            ->where('bookings.screening_id', $request->screening_id)
            ->where('bookings.status', 'active')
            ->exists();

        if ($exists) {
            return $this->returnError('มีบางที่นั่งถูกจองไปแล้ว กรุณาโหลดใหม่', 409);
        }
        
        $this->log('เพิ่มที่นั่ง', "เพิ่มที่นั่ง: {$seat->seat_number} ห้อง ID: {$seat->screening_room_id}");

        return $this->returnCreated($seat);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'seat_number' => 'required|string|max:255',
            'row' => 'required|integer|min:0',
            'column' => 'required|integer|min:0',
            'seat_type' => 'required|string|max:255',
            'is_active' => 'required|boolean',
        ]);

        $seat = Seat::find($id);
        if (!$seat) {
            return $this->returnNotFound('ไม่พบข้อมูลที่นั่ง');
        }

        $seat->seat_number = $request->seat_number;
        $seat->row = $request->row;
        $seat->column = $request->column;
        $seat->seat_type = $request->seat_type;
        $seat->is_active = $request->is_active;
        $seat->save();
        if (!$seat) {
            return $this->returnError('แก้ไขข้อมูลไม่สำเร็จ', 500);
        }

        $this->log('แก้ไขที่นั่ง', "แก้ไขที่นั่ง: {$seat->seat_number} (ID: {$id})");

        return $this->returnSuccess('แก้ไขที่นั่งสำเร็จ');
    }

    public function destroy($id)
    {
        $seat = Seat::find($id);
        if (!$seat) {
            return $this->returnNotFound('ไม่พบข้อมูลที่นั่ง');
        }

        $seat->delete();

        $this->log('ลบที่นั่ง', "ลบที่นั่ง: {$seat->seat_number} (ID: {$id})");

        return $this->returnSuccess('ลบที่นั่งสำเร็จ');
    }
}
