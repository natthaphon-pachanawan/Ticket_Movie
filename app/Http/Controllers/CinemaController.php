<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cinema;

class CinemaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cinema = Cinema::with('province', 'district', 'subdistrict')
            ->get();
        return $this->returnJson($cinema);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'province_id' => 'required|exists:provinces,id',
            'district_id' => 'required|exists:districts,id',
            'subdistrict_id' => 'required|exists:subdistricts,id',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email|max:255',
        ]);

        $cinema = Cinema::create([
                'name' => $request->name,
                'address' => $request->address,
                'province_id' => $request->province_id,
                'district_id' => $request->district_id,
                'subdistrict_id' => $request->subdistrict_id,
                'contact_phone' => $request->contact_phone,
                'contact_email' => $request->contact_email,
            ]);
        if (!$cinema) {
            return $this->returnError('เพิ่มข้อมูลไม่สำเร็จ', 500);
        }

        $this->log('เพิ่มโรงหนัง', "เพิ่มโรงหนังชื่อ: {$cinema->name}");

        return $this->returnCreated($cinema);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $cinema = Cinema::with('province', 'district', 'subdistrict')
            ->find($id);
        if (!$cinema) {
            return $this->returnNotFound('ไม่พบข้อมูลโรงภาพยนตร์ที่ระบุ');
        }
        return $this->returnJson($cinema);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'province_id' => 'required|exists:provinces,id',
            'district_id' => 'required|exists:districts,id',
            'subdistrict_id' => 'required|exists:subdistricts,id',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email|max:255',
        ]);

        $cinema = Cinema::find($id);
        if (!$cinema) {
            return $this->returnNotFound('ไม่พบข้อมูลโรงภาพยนตร์ที่ระบุ');
        }

        $cinema->name = $request->name;
        $cinema->address = $request->address;
        $cinema->province_id = $request->province_id;
        $cinema->district_id = $request->district_id;
        $cinema->subdistrict_id = $request->subdistrict_id;
        $cinema->contact_phone = $request->contact_phone;
        $cinema->contact_email = $request->contact_email;
        $cinema->save();
        if (!$cinema) {
            return $this->returnError('อัปเดตข้อมูลไม่สำเร็จ', 500);
        }

        $this->log('แก้ไขโรงหนัง', "แก้ไขข้อมูลโรงหนัง ID: {$cinema->id}");

        return $this->returnSuccess('อัปเดตข้อมูลโรงภาพยนตร์เรียบร้อยแล้ว');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $cinema = Cinema::find($id);
        if (!$cinema) {
            return $this->returnNotFound('ไม่พบข้อมูลโรงภาพยนตร์ที่ระบุ');
        }

        $cinema->delete();

        $this->log('ลบโรงหนัง', "ลบโรงหนังชื่อ: {$cinema->name} (ID: {$cinema->id})");
        
        return $this->returnSuccess('ลบข้อมูลโรงภาพยนตร์เรียบร้อยแล้ว');
    }
}
