<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function salesSummary(Request $request)
    {
        $from = $request->query('date_start', Carbon::now()->subDays(30)->toDateString());
        $to   = $request->query('date_end',   Carbon::now()->toDateString());

        $totalRevenue = Ticket::whereBetween('issued_at', ["$from 00:00:00", "$to 23:59:59"])
            ->sum('price');

        $totalBookings = Ticket::distinct('booking_id')
            ->whereBetween('issued_at', ["$from 00:00:00", "$to 23:59:59"])
            ->count('booking_id');

        $daily = Ticket::selectRaw("DATE(issued_at) as date,
                                COUNT(*) as tickets_sold,
                                SUM(price) as revenue")
            ->whereBetween('issued_at', ["$from 00:00:00", "$to 23:59:59"])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return $this->returnJson([
            'date_start' => $from,
            'date_end'   => $to,
            'total_revenue' => $totalRevenue,
            'total_bookings' => $totalBookings,
            'daily' => $daily,
        ]);
    }
}
