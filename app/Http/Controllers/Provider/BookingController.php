<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Notifications\BookingConfirmed;
use App\Notifications\BookingCancelled;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    
    public function index()
    {
        $bookings = Booking::whereHas('slot.service', fn($q) =>
            $q->where('provider_id', auth()->id())
        )
        ->with(['customer', 'slot.service', 'payment'])
        ->latest()
        ->paginate(15);

        return view('provider.bookings.index', compact('bookings'));
    }

    public function confirm(Booking $booking)
    {
        $this->authorize('manage', $booking);
        abort_unless($booking->isPending(), 422, 'الحجز ليس في حالة انتظار.');

        $booking->loadMissing('slot.service', 'payment');
        if ($booking->slot->service->price > 0 && !$booking->payment?->isVerified()) {
            abort(422, 'يجب توثيق الدفع أولاً قبل تأكيد الحجز.');
        }

        $booking->update(['status' => 'confirmed']);
        $booking->customer->notify(new BookingConfirmed($booking));

        return back()->with('success', 'تم تأكيد الحجز.');
    }

    public function cancel(Request $request, Booking $booking)
    {
        $this->authorize('manage', $booking);
        abort_unless($booking->canBeCancelled(), 422, 'لا يمكن إلغاء هذا الحجز.');

        $request->validate(['reason' => 'nullable|string|max:300']);

        $booking->slot->update(['is_booked' => false]);
        $booking->update([
            'status'              => 'cancelled',
            'cancelled_at'        => now(),
            'cancellation_reason' => $request->reason,
        ]);

        $booking->customer->notify(new BookingCancelled($booking));
        return back()->with('success', 'تم إلغاء الحجز.');
    }

    public function complete(Booking $booking)
    {
        $this->authorize('manage', $booking);
        abort_unless($booking->isConfirmed(), 422, 'الحجز غير مؤكد.');

        $booking->update(['status' => 'completed']);
        return back()->with('success', 'تم تمييز الحجز كمكتمل.');
    }
}
