<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Booking, Payment, Service, User};

class DashboardController extends Controller
{

    public function index()
    {
        $stats = [
            'total_users'    => User::count(),
            'total_services' => Service::count(),
            'total_bookings' => Booking::count(),
            'pending'        => Booking::where('status', 'pending')->count(),
            'confirmed'      => Booking::where('status', 'confirmed')->count(),
            'cancelled'      => Booking::where('status', 'cancelled')->count(),
            'revenue'        => Booking::where('status', 'completed')
                ->with('slot.service')
                ->get()
                ->sum(fn($b) => $b->slot->service->price),
            'payments_pending' => Payment::where('status', 'submitted')->count(),
            'payments_verified' => Payment::where('status', 'verified')->count(),
        ];

        $recentBookings = Booking::with(['customer', 'slot.service.provider'])
            ->latest()->limit(10)->get();

        $topServices = Service::withCount(['slots as bookings_count' => fn($q) =>
            $q->whereHas('booking', fn($q2) => $q2->where('status', 'completed'))
        ])->orderByDesc('bookings_count')->limit(5)->get();

        return view('admin.dashboard', compact('stats', 'recentBookings', 'topServices'));
    }
}
