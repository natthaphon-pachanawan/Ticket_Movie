<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\Mpdf;

class ReportController extends Controller
{
    public function salesSummary(Request $request)
    {
        $date_start = $request->query('date_start', Carbon::now()->subDays(30)->toDateString());
        $date_end   = $request->query('date_end',   Carbon::now()->toDateString());

        $totalRevenue = Ticket::whereBetween('issued_at', ["$date_start 00:00:00", "$date_end 23:59:59"])
            ->sum('price');

        $totalBookings = Ticket::distinct('booking_id')
            ->whereBetween('issued_at', ["$date_start 00:00:00", "$date_end 23:59:59"])
            ->count('booking_id');

        $daily = Ticket::selectRaw("DATE(issued_at) as date,
                                COUNT(*) as tickets_sold,
                                SUM(price) as revenue")
            ->whereBetween('issued_at', ["$date_start 00:00:00", "$date_end 23:59:59"])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return $this->returnJson([
            'date_start' => $date_start,
            'date_end'   => $date_end,
            'total_revenue' => $totalRevenue,
            'total_bookings' => $totalBookings,
            'daily' => $daily,
        ]);
    }

    public function salesReportPdf(Request $request)
    {
        // 1) ตั้งค่า mPDF + ฟอนต์ไทย
        $defaultConfig     = (new ConfigVariables())->getDefaults();
        $fontDirs          = $defaultConfig['fontDir'];
        $defaultFontConfig = (new FontVariables())->getDefaults();
        $fontData          = $defaultFontConfig['fontdata'];

        $mpdf = new Mpdf([
            'mode'         => 'utf-8',
            'format'       => 'A4',
            'default_font' => 'th-sarabun',
            'fontDir'      => array_merge($fontDirs, [
                public_path('storage/fonts/thsarabun'),
            ]),
            'fontdata'     => $fontData + [
                'th-sarabun' => [
                    'R'  => 'THSarabun.ttf',
                    'B'  => 'THSarabun Bold.ttf',
                    'I'  => 'THSarabun Italic.ttf',
                    'BI' => 'THSarabun BoldItalic.ttf',
                ],
            ],
            'margin_left'   => 10,
            'margin_right'  => 10,
            'margin_top'    => 10,
            'margin_bottom' => 10,
        ]);

        // 2) อ่านช่วงวันที่จาก query string (default 30 วันย้อนหลัง)
        $date_start = $request->query('date_start', Carbon::now()->toDateString());
        $date_end   = $request->query('date_end',   Carbon::now()->toDateString());

        // 3) คำนวณยอดรวม และจำนวน booking
        $totalRevenue = Ticket::whereBetween('issued_at', ["{$date_start} 00:00:00", "{$date_end} 23:59:59"])
            ->sum('price');

        $totalBookings = Ticket::distinct('booking_id')
            ->whereBetween('issued_at', ["{$date_start} 00:00:00", "{$date_end} 23:59:59"])
            ->count('booking_id');

        // 4) สรุปรายวัน
        $daily = Ticket::selectRaw("DATE(issued_at) as date,
                                   COUNT(*) as tickets_sold,
                                   SUM(price) as revenue")
            ->whereBetween('issued_at', ["{$date_start} 00:00:00", "{$date_end} 23:59:59"])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // 5) สร้าง HTML
        $html = '
        <style>
          body { font-family: th-sarabun, sans-serif; font-size:12pt; }
          h1, h2 { text-align: center; margin:0; }
          .summary { margin: 1em 0; }
          .summary div { margin-bottom: 0.5em; }
          table { width:100%; border-collapse: collapse; margin-top:1em; }
          th, td { border:1px solid #333; padding:4px; text-align:center; }
          th { background:#f0f0f0; }
        </style>
        <h1>รายงานยอดขายตั๋วภาพยนตร์</h1>
        <h2>ช่วง ' . $date_start . ' ถึง ' . $date_end . '</h2>
        <div class="summary">
          <div><strong>ยอดขายรวม:</strong> ' . number_format($totalRevenue) . ' บาท</div>
          <div><strong>จำนวนตั๋ว:</strong> ' . $totalBookings . ' รายการ</div>
        </div>
        <table>
          <thead>
            <tr>
              <th>วันที่</th>
              <th>ตั๋วที่ขาย</th>
              <th>รายได้ (บาท)</th>
            </tr>
          </thead>
          <tbody>';
        foreach ($daily as $d) {
            $html .= '<tr>
                        <td>' . $d->date . '</td>
                        <td>' . $d->tickets_sold . '</td>
                        <td>' . number_format($d->revenue) . '</td>
                      </tr>';
        }
        $html .= '</tbody></table>';

        // 6) เขียน PDF และส่งกลับแบบ inline
        $mpdf->SetTitle("Sales_Report_{$date_start}_to_{$date_end}");
        $mpdf->WriteHTML($html);
        $pdfContent = $mpdf->Output('', 'S');

        return Response::make($pdfContent, 200, [
          'Content-Type'        => 'application/pdf',
          'Content-Disposition' => 'inline; filename="Sales_Report_'.$date_start.'_to_'.$date_end.'.pdf"',
          'Accept-Ranges'       => 'bytes',
        ]);
    }
}
