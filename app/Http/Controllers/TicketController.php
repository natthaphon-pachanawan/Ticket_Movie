<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TicketController extends Controller
{
    // สร้างตั๋วใหม่ (หลังจากที่การจองถูกชำระเงิน)
    public function store(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'screening_id' => 'required|exists:screenings,id',
            'seat_id' => 'required|exists:seats,id',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:active,used,cancelled',
        ]);

        // สร้าง ticket_code แบบสุ่ม (สามารถปรับเปลี่ยนรูปแบบได้)
        $ticketCode = strtoupper(Str::random(10));

        $ticket = Ticket::create([
            'booking_id' => $request->booking_id,
            'screening_id' => $request->screening_id,
            'seat_id' => $request->seat_id,
            'ticket_code' => $ticketCode,
            'price' => $request->price,
            'status' => $request->status,
            'issued_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);

        if (!$ticket) {
            return $this->returnError('สร้างตั๋วไม่สำเร็จ', 500);
        }

        $this->log('สร้างตั๋ว', "สร้างตั๋ว รหัส: {$ticket->ticket_code} สำหรับ booking ID: {$ticket->booking_id}");

        return $this->returnCreated($ticket);
    }

    // แสดงตั๋ว (หรือรายการตั๋ว)
    public function index()
    {
        $tickets = Ticket::with(['booking', 'screening', 'seat'])->get();
        return $this->returnJson($tickets);
    }

    // ดูรายละเอียดตั๋ว
    public function show($id)
    {
        $ticket = Ticket::with(['booking', 'screening', 'seat'])->find($id);
        if (!$ticket) {
            return $this->returnNotFound('ไม่พบข้อมูลตั๋ว');
        }
        return $this->returnJson($ticket);
    }

    // อัปเดตตั๋ว (เช่น เปลี่ยนสถานะเป็น used เมื่อมีการเข้าโรง)
    public function update(Request $request, $id)
    {
        $ticket = Ticket::find($id);
        if (!$ticket) {
            return $this->returnNotFound('ไม่พบข้อมูลตั๋ว');
        }

        $request->validate([
            'status' => 'required|in:active,used,cancelled',
        ]);

        $ticket->update($request->only('status'));
        $this->log('อัปเดตตั๋ว', "อัปเดตสถานะตั๋ว รหัส: {$ticket->ticket_code} เป็น: {$ticket->status}");

        return $this->returnSuccess('อัปเดตตั๋วเรียบร้อยแล้ว');
    }

    // ลบตั๋ว (ถ้าจำเป็น)
    public function destroy($id)
    {
        $ticket = Ticket::find($id);
        if (!$ticket) {
            return $this->returnNotFound('ไม่พบข้อมูลตั๋ว');
        }

        $this->log('ลบตั๋ว', "ลบตั๋ว รหัส: {$ticket->ticket_code}");
        $ticket->delete();

        return $this->returnSuccess('ลบตั๋วสำเร็จ');
    }
}
