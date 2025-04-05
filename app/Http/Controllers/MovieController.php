<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Movie;

class MovieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $movies = Movie::all();
        return $this->returnJson($movies);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {


        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'genre' => 'required|string|max:255',
            'director' => 'required|string|max:255',
            'cast' => 'required|string|max:255',
            'duration' => 'required|integer|min:1',
            'release_date' => 'required|date',
            'poster_url' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $posterPath = $request->file('poster_url')->store('posters', 'public');

        $movie = Movie::create([
            'title' => $request->title,
            'description' => $request->description,
            'genre' => $request->genre,
            'director' => $request->director,
            'cast' => $request->cast,
            'duration' => $request->duration,
            'release_date' => $request->release_date,
            'poster_url' => $posterPath,
        ]);
        if (!$movie) {
            return $this->returnError('เพิ่มข้อมูลไม่สำเร็จ', 500);
        }

        return $this->returnCreated($movie);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $movie = Movie::find($id);
        if (!$movie) {
            return $this->returnNotFound('ไม่พบข้อมูลภาพยนตร์ที่ต้องการ', 404);
        }
        return $this->returnJson($movie);
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, string $id)
    {
        $movie = Movie::find($id);
        if (!$movie) {
            return $this->returnNotFound('ไม่พบข้อมูลภาพยนตร์ที่ต้องการ', 404);
        }

        $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'genre' => 'sometimes|string|max:255',
            'director' => 'sometimes|string|max:255',
            'cast' => 'sometimes|string|max:255',
            'duration' => 'sometimes|integer|min:1',
            'release_date' => 'sometimes|date',
            'poster_url' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // อัปเดตข้อมูล
        $movie->title = $request->title;
        $movie->description = $request->description;
        $movie->genre = $request->genre;
        $movie->director = $request->director;
        $movie->cast = $request->cast;
        $movie->duration = $request->duration;
        $movie->release_date = $request->release_date;

        // ถ้ามีการส่งรูปภาพใหม่
        if ($request->hasFile('poster_url')) {
            // ลบรูปเก่า
            if ($movie->poster_url && Storage::disk('public')->exists($movie->poster_url)) {
                Storage::disk('public')->delete($movie->poster_url);
            }

            // อัปโหลดรูปใหม่
            $posterPath = $request->file('poster_url')->store('posters', 'public');
            $movie->poster_url = $posterPath;
        }
        $movie->save();

        return $this->returnSuccess('อัปเดตข้อมูลสำเร็จ', 200);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $movie = Movie::find($id);
        if (!$movie) {
            return $this->returnNotFound('ไม่พบข้อมูลภาพยนตร์ที่ต้องการ', 404);
        }

        $movie->delete();

        return $this->returnJson('ลบข้อมูลสำเร็จ', 200);
    }
}
