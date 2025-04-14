<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use Carbon\Carbon;

class BookingController extends Controller
{
    // แสดงรายการการจองทั้งหมด (หรืออาจเพิ่ม filter สำหรับ user ที่ login อยู่)
    public function index()
    {
        $bookings = Booking::with(['user', 'screening'])->get();
        return $this->returnJson($bookings);
    }

    // สร้างการจองใหม่
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'screening_id' => 'required|exists:screenings,id',
            'booking_datetime' => 'required|date_format:Y-m-d H:i:s',
            'total_price' => 'required|numeric|min:0',
            'status' => 'required|in:active,cancelled,expired',
            'cancellation_reason' => 'nullable|string',
        ]);

        // บันทึกเวลาจอง (ในกรณีที่ผู้ใช้ส่งค่ามาเองไม่ครบ)
        $bookingData = $request->all();
        if (!isset($bookingData['booking_datetime'])) {
            $bookingData['booking_datetime'] = Carbon::now()->format('Y-m-d H:i:s');
        }

        $booking = Booking::create($bookingData);
        if (!$booking) {
            return $this->returnError('เพิ่มข้อมูลการจองไม่สำเร็จ', 500);
        }
        // log การจอง
        $this->log('เพิ่มการจอง', "ผู้ใช้ ID: {$booking->user_id} จองรอบฉาย ID: {$booking->screening_id} รวมราคา: {$booking->total_price}");

        return $this->returnCreated($booking);
    }

    // ดูรายละเอียดการจอง
    public function show($id)
    {
        $booking = Booking::with(['user', 'screening'])->find($id);
        if (!$booking) {
            return $this->returnNotFound('ไม่พบข้อมูลการจอง');
        }
        return $this->returnJson($booking);
    }

    // อัปเดตสถานะการจอง (เช่น ยกเลิก)
    public function update(Request $request, $id)
    {
        $booking = Booking::find($id);
        if (!$booking) {
            return $this->returnNotFound('ไม่พบข้อมูลการจอง');
        }

        $request->validate([
            'status' => 'required|in:active,cancelled,expired',
            'cancellation_reason' => 'nullable|string',
        ]);

        $booking->update($request->only('status', 'cancellation_reason'));
        $this->log('แก้ไขการจอง', "แก้ไขการจอง ID: {$booking->id} เปลี่ยนสถานะเป็น: {$booking->status}");

        return $this->returnSuccess('อัปเดตการจองเรียบร้อย');
    }

    // ลบการจอง (ถ้าอนุญาตให้ลบได้)
    public function destroy($id)
    {
        $booking = Booking::find($id);
        if (!$booking) {
            return $this->returnNotFound('ไม่พบข้อมูลการจอง');
        }

        $this->log('ลบการจอง', "ลบการจอง ID: {$booking->id}");
        $booking->delete();

        return $this->returnSuccess('ลบการจองสำเร็จ');
    }
}
