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
        // 1) ไล่สถานะ expired ก่อน
        app(\App\Http\Controllers\BookingController::class)
            ->expireOldBookings();

        // 2) โหลด screening_id จาก query string
        $screeningId = request()->query('screening_id');

        // 3) ดึงที่นั่งโดยกรองเฉพาะแถวที่ยังไม่ถูก soft‑delete (deleted_at IS NULL)
        $seats = DB::table('seats')
            ->whereNull('deleted_at')                                 // เพิ่มตรงนี้ให้ไม่เอา soft‑deleted
            ->where('screening_room_id', $screening_room_id)
            ->select('seats.*')
            ->selectRaw("
            EXISTS(
                SELECT 1
                FROM booking_seats bs
                JOIN bookings b ON b.id = bs.booking_id
                WHERE bs.seat_id     = seats.id
                  AND b.screening_id = ?
                  AND b.status       = 'active'
            ) AS is_reserved
        ", [$screeningId])
            ->get();

        return $this->returnJson($seats);
    }


    public function store(Request $request)
    {
        $mode = $request->input('mode', 'single');

        if ($mode === 'single') {
            // สร้างทีละตัว
            $data = $request->validate([
                'screening_room_id' => 'required|exists:screening_rooms,id',
                'seat_number'       => 'required|string|max:255',
                'row'               => 'required|integer|min:0',
                'column'            => 'required|integer|min:0',
                'seat_type'         => 'required|string|max:255',
                'is_active'         => 'required|boolean',
            ]);

            $seat = Seat::create($data);
            $this->log('เพิ่มที่นั่ง', "Seat {$seat->seat_number} ห้อง {$seat->screening_room_id}");
            return $this->returnCreated($seat);
        }

        // --- bulk mode: รับ array ของ seats จาก front-end ---
        $data = $request->validate([
            'screening_room_id'   => 'required|exists:screening_rooms,id',
            'seats'               => 'required|array',
            'seats.*.row'         => 'required|integer|min:1',
            'seats.*.column'      => 'required|integer|min:1',
            'seats.*.seat_number' => 'required|string',
            'seats.*.seat_type'   => 'required|string',
            'is_active'           => 'required|boolean',
        ]);

        $created = [];
        foreach ($data['seats'] as $s) {
            // ข้ามถ้าตำแหน่งซ้ำ
            $exists = Seat::where('screening_room_id', $data['screening_room_id'])
                ->where('row', $s['row'])
                ->where('column', $s['column'])
                ->exists();
            if ($exists) continue;

            $seat = Seat::create([
                'screening_room_id' => $data['screening_room_id'],
                'row'               => $s['row'],
                'column'            => $s['column'],
                'seat_number'       => $s['seat_number'],
                'seat_type'         => $s['seat_type'],
                'is_active'         => $data['is_active'],
            ]);
            $created[] = $seat;
        }

        $count = count($created);
        $this->log('bulk-create-seats', "สร้างที่นั่งจำนวน {$count} ตัว ในห้อง {$data['screening_room_id']}");

        return $this->returnCreated([
            'message' => "สร้างที่นั่งสำเร็จ {$count} ตัว",
            'seats'   => $created,
        ]);
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
