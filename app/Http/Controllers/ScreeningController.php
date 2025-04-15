<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Screening;

class ScreeningController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $screening = Screening::with('movie', 'screeningRoom.cinema')
            ->get();

        return $this->returnJson($screening);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'movie_id' => 'required|exists:movies,id',
            'screening_room_id' => 'required|exists:screening_rooms,id',
            'screening_datetime' => 'required|date_format:Y-m-d H:i:s',
            'price' => 'required|numeric|min:0',
        ]);

        $screening = Screening::create([
            'movie_id' => $request->movie_id,
            'screening_room_id' => $request->screening_room_id,
            'screening_datetime' => $request->screening_datetime,
            'price' => $request->price,
        ]);
        if (!$screening) {
            return $this->returnError('ไม่สามารถเพิ่มรอบฉายได้');
        }

        $this->log('เพิ่มรอบฉาย', "หนัง: {$screening->movie_id} ห้อง: {$screening->screening_room_id} เวลา: {$screening->screening_datetime}");

        return $this->returnCreated($screening);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $screening = Screening::with('movie', 'screeningRoom.cinema')
            ->find($id);
        if (!$screening) {
            return $this->returnNotFound('ไม่พบข้อมูลรอบฉาย');
        }

        return $this->returnJson($screening);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        $request->validate([
            'movie_id' => 'sometimes|exists:movies,id',
            'screening_room_id' => 'sometimes|exists:screening_rooms,id',
            'screening_datetime' => 'sometimes|date_format:Y-m-d H:i:s',
            'price' => 'sometimes|numeric|min:0',
        ]);

        $screening = Screening::find($id);

        $screening->movie_id = $request->movie_id;
        $screening->screening_room_id = $request->screening_room_id;
        $screening->screening_datetime = $request->screening_datetime;
        $screening->price = $request->price;
        $screening->save();

        if (!$screening) {
            return $this->returnError('ไม่สามารถอัปเดตรอบฉายได้');
        }

        $this->log('อัปเดตรอบฉาย', "อัปเดต ID: {$screening->id}");

        return $this->returnSuccess('อัปเดตรอบฉายสำเร็จ');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $screening = Screening::find($id);
        if (!$screening) {
            return $this->returnNotFound('ไม่พบข้อมูลการฉาย');
        }
        $screening->delete();

        $this->log('ลบรอบฉาย', "ลบรอบฉาย ID: {$screening->id}");

        return $this->returnSuccess('ลบรอบฉายสำเร็จ');
    }

    public function listByMovie(Request $request)
    {
        $movie_id = $request->input('movie_id');

        $screenings = Screening::with('movie', 'screeningRoom.cinema')
            ->where('movie_id', $movie_id)
            ->get();

        return $this->returnJson($screenings);
    }

    public function filterByMovieAndDate(Request $request)
    {
        $movie_id = $request->input('movie_id');
        $date = $request->input('date');

        $screenings = Screening::with('movie', 'screeningRoom.cinema')
            ->where('movie_id', $movie_id)
            ->whereDate('screening_datetime', $date)
            ->orderBy('screening_datetime')
            ->get();

        return $this->returnJson($screenings);
    }
}
