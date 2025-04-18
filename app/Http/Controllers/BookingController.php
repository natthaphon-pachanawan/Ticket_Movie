<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    // แสดงรายการการจองทั้งหมด (หรืออาจเพิ่ม filter สำหรับ user ที่ login อยู่)
    public function index()
    {
        $bookings = Booking::with(['screening', 'screening.movie', 'tickets', 'seats'])
            ->where('user_id', Auth::user()->id)
            ->latest()
            ->get();

        return $this->returnJson($bookings);
    }


    // สร้างการจองใหม่
    public function store(Request $request)
    {
        $request->validate([
            'screening_id' => 'required|exists:screenings,id',
            'booking_datetime' => 'required|date_format:Y-m-d H:i:s',
            'total_price' => 'required|numeric|min:0',
            'status' => 'required|in:active,cancelled,expired',
            'cancellation_reason' => 'nullable|string',
            'seats' => 'required|array',              // ต้องมี seat_ids
            'seats.*' => 'required|exists:seats,id',    // ตรวจสอบแต่ละค่าใน array
        ]);

        $booking = Booking::create([
            'user_id' => $request->user()->id,
            'screening_id' => $request->screening_id,
            'booking_datetime' => $request->booking_datetime,
            'total_price' => $request->total_price,
            'status' => $request->status,
            'cancellation_reason' => $request->cancellation_reason
        ]);

        if (!$booking) {
            return $this->returnError('สร้างการจองไม่สำเร็จ', 500);
        }

        $seatIds = $request->input('seats', []); // ✅ กำหนดก่อนใช้

        foreach ($seatIds as $seatId) {
            DB::table('booking_seats')->insert([
                'booking_id' => $booking->id,
                'seat_id' => $seatId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }


        $this->log('เพิ่มการจอง', "ผู้ใช้ ID: {$booking->user_id} จองรอบฉาย ID: {$booking->screening_id}");

        return $this->returnJson($booking);
    }

    // ตัวอย่างฟังก์ชัน generateTicketCode()
    protected function generateTicketCode()
    {
        return strtoupper(uniqid('TICKET_'));
    }


    // ดูรายละเอียดการจอง
    public function show($id)
    {
        $booking = Booking::with([
            'user',
            'screening.movie',
            'tickets',
            'seats' // ✅ เพิ่มตรงนี้
        ])->find($id);

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

        $booking->status = $request->status;
        $booking->cancellation_reason = $request->cancellation_reason;
        $booking->save();

        if (!$booking) {
            return $this->returnError('อัปเดตการจองไม่สําเร็จ', 500);
        }

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

    public function cancel($id)
    {
        // 1. หา Booking พร้อมข้อมูล screening
        $booking = Booking::with('screening')->find($id);
        if (! $booking) {
            return $this->returnNotFound('ไม่พบการจองที่ต้องการ');
        }

        // 2. ตรวจว่าเกินเวลาฉายหรือยัง
        $now = Carbon::now();
        if ($now->gte($booking->screening->screening_datetime)) {
            return $this->returnError('ไม่สามารถยกเลิกหลังเวลาฉายได้', 403);
        }

        // 3. อัปเดตสถานะเป็น cancelled
        $booking->status = 'cancelled';
        $booking->save();

        // 4. คืนที่นั่ง: ลบ pivot booking_seats
        DB::table('booking_seats')
            ->where('booking_id', $id)
            ->delete();

        // 5. (ถ้าใช้ is_reserved) รีเซ็ตฟิลด์ is_reserved
        // DB::table('seats')->whereIn('id', $seatIds)->update(['is_reserved' => false]);

        // 6. Log action
        $this->log('ยกเลิกการจอง', "ยกเลิกการจอง ID: {$id}");

        // 7. ตอบกลับ
        return $this->returnSuccess('ยกเลิกการจองเรียบร้อยแล้ว');
    }
}
