<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Slip;
use Illuminate\Support\Facades\Storage;

class SlipController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $slip = Slip::with('booking')
            ->get();

        return $this->returnJson($slip);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'slip_image_url' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'amount' => 'required|numeric|min:0',
            'payment_status' => 'required|in:pending,confirmed,rejected',
            'payment_date' => 'nullable|date_format:Y-m-d H:i:s',
        ]);

        // อัปโหลดรูป slip
        $slipPath = $request->file('slip_image_url')->store('slips', 'public');

        $slip = Slip::create([
            'booking_id' => $request->booking_id,
            'slip_image_url' => $slipPath,
            'amount' => $request->amount,
            'payment_status' => $request->payment_status,
            'payment_date' => $request->payment_date,
        ]);

        if (!$slip) {
            return $this->returnError('เพิ่มข้อมูล slip ไม่สำเร็จ', 500);
        }

        $this->log('เพิ่ม slip', "Slip สำหรับ booking ID: {$slip->booking_id} สถานะ: {$slip->payment_status}");

        return $this->returnCreated($slip);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $slip = Slip::with('booking')
            ->find($id);
        if (!$slip) {
            return $this->returnJson(null, 'ไม่พบสลิปที่ระบุ', 404);
        }

        return $this->returnJson($slip);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'payment_status' => 'required|in:pending,confirmed,rejected',
            'payment_date' => 'nullable|date_format:Y-m-d H:i:s',
            'slip_image_url' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $slip = Slip::find($id);

        if (!$slip) {
            return $this->returnError('ไม่พบ slip ที่จะอัปเดต', 404);
        }

        // ถ้ามีการส่งไฟล์รูปใหม่มา
        if ($request->hasFile('slip_image_url')) {
            // ลบรูปเก่า
            if ($slip->slip_image_url && Storage::disk('public')->exists($slip->slip_image_url)) {
                Storage::disk('public')->delete($slip->slip_image_url);
            }

            // อัปโหลดรูปใหม่
            $newPath = $request->file('slip_image_url')->store('slips', 'public');
            $slip->slip_image_url = $newPath;
        }

        // อัปเดตสถานะ + วันเวลา
        $slip->payment_status = $request->payment_status;
        $slip->payment_date = $request->payment_date;
        $slip->save();

        $this->log('แก้ไข slip', "Slip ID: {$slip->id} อัปเดตสถานะเป็น: {$slip->payment_status}");

        return $this->returnSuccess('อัปเดต slip สำเร็จแล้ว');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $slip = Slip::find($id);
        if (!$slip) {
            return $this->returnJson(null, 'ไม่พบสลิปที่ระบุ', 404);
        }
        if ($slip->image) {
            Storage::disk('public')->delete($slip->image);
        }
        $slip->delete();
        $this->log('ลบ slip', "ลบ slip ID: {$slip->id}");

        return $this->returnSuccess('ลบ slip สำเร็จ');
    }
}
