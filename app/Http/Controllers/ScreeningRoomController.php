<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ScreeningRoom;

class ScreeningRoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $screening_room = ScreeningRoom::with('cinema')
        ->get();

        return $this->returnJson($screening_room);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'cinema_id' => 'required|exists:cinemas,id',
            'room_name' => 'required|string|max:255',
            'seat_capacity' => 'required|integer|min:0',
            'description' => 'nullable|string',
        ]);

        $screening_room = ScreeningRoom::create([
            'cinema_id' => $request->cinema_id,
            'room_name' => $request->room_name,
            'seat_capacity' => $request->seat_capacity,
            'description' => $request->description,
        ]);

        if (!$screening_room) {
            return $this->returnError('เพิ่มข้อมูลไม่สำเร็จ', 500);
        }

        $this->log('เพิ่มห้องฉาย', "เพิ่มห้องฉาย {$screening_room->room_name}");

        return $this->returnCreated($screening_room);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $screening_room = ScreeningRoom::with('cinema', 'seats')
        ->find($id);
        if (!$screening_room) {
            return $this->returnNotFound('ไม่พบข้อมูลห้องฉาย');
        }

        return $this->returnJson($screening_room);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'cinema_id' => 'required|exists:cinemas,id',
            'room_name' => 'required|string|max:255',
            'seat_capacity' => 'required|integer|min:0',
            'description' => 'nullable|string',
        ]);

        $screening_room = ScreeningRoom::find($id);
        if (!$screening_room) {
            return $this->returnNotFound('ไม่พบข้อมูลห้องฉาย');
        }

        $screening_room->cinema_id = $request->cinema_id;
        $screening_room->room_name = $request->room_name;
        $screening_room->seat_capacity = $request->seat_capacity;
        $screening_room->description = $request->description;
        $screening_room->save();
        if (!$screening_room) {
            return $this->returnError('อัปเดตข้อมูลไม่สำเร็จ', 500);
        }
        $this->log('แก้ไขห้องฉาย', "แก้ไขห้องฉาย ID: {$screening_room->id}");

        return $this->returnJson($screening_room);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $screening_room = ScreeningRoom::find($id);
        if (!$screening_room) {
            return $this->returnNotFound('ไม่พบข้อมูลห้องฉาย');
        }

        $screening_room->delete();

        $this->log('ลบห้องฉาย', "ลบห้องฉาย ID: {$screening_room->id}");

        return $this->returnSuccess('ลบข้อมูลห้องฉายเรียบร้อยแล้ว');
    }
}
