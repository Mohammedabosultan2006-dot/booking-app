<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /** عرض الخدمات المتاحة */
    public function index(Request $request)
    {
        $search = $request->string('q')->trim()->toString();

        $services = Service::active()
            ->with(['provider', 'reviews'])
            ->withCount(['slots as available_slots_count' => fn($q) =>
                $q->where('is_booked', false)->where('start_at', '>', now())
            ])
            ->when($search, fn($q) => $q->where(fn($q2) =>
                $q2->where('name', 'like', "%{$search}%")
                   ->orWhere('description', 'like', "%{$search}%")
                   ->orWhere('location', 'like', "%{$search}%")
            ))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $totalProviders = User::where('role', 'provider')->count();

        return view('services.index', compact('services', 'search', 'totalProviders'));
    }

    /** تفاصيل الخدمة + الأوقات المتاحة */
    public function show(Service $service)
    {
        $service->load(['provider', 'reviews.customer']);

        $slots = $service->slots()
            ->available()
            ->upcoming()
            ->get()
            ->groupBy(fn($s) => $s->start_at->format('Y-m-d'));

        return view('services.show', compact('service', 'slots'));
    }
}
