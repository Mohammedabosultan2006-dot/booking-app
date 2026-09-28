<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Review;

class DashboardController extends Controller
{
    public function index()
    {
        $provider = auth()->user();
        $serviceIds = $provider->services()->pluck('id');

        $bookingsQuery = Booking::whereHas('slot', fn($q) => $q->whereIn('service_id', $serviceIds));

        $stats = [
            'today_bookings'   => (clone $bookingsQuery)->whereHas('slot', fn($q) =>
                $q->whereDate('start_at', today())
            )->count(),
            'pending_bookings' => (clone $bookingsQuery)->where('status', 'pending')->count(),
            'pending_payments' => Payment::where('provider_id', $provider->id)->where('status', 'submitted')->count(),
            'month_revenue'    => Payment::where('provider_id', $provider->id)
                ->where('status', 'verified')
                ->whereMonth('verified_at', now()->month)
                ->whereYear('verified_at', now()->year)
                ->sum('amount'),
            'avg_rating'       => round(Review::whereIn('service_id', $serviceIds)->avg('rating') ?? 0, 1),
            'total_services'   => $serviceIds->count(),
        ];

        $recentBookings = (clone $bookingsQuery)
            ->with(['customer', 'slot.service', 'payment'])
            ->latest()
            ->limit(6)
            ->get();

        $pendingPayments = Payment::where('provider_id', $provider->id)
            ->where('status', 'submitted')
            ->with(['customer', 'booking.slot.service'])
            ->latest('submitted_at')
            ->limit(5)
            ->get();

        return view('provider.dashboard', compact('stats', 'recentBookings', 'pendingPayments'));
    }
}
