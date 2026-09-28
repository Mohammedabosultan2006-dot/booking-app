<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Slot;
use App\Notifications\BookingCreated;
use App\Notifications\BookingCancelled;
use App\Notifications\BookingConfirmed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{

    /** عرض حجوزات العميل */
    public function index()
    {
        $bookings = auth()->user()->bookings()
            ->with(['slot.service.provider'])
            ->latest()
            ->paginate(10);

        return view('bookings.index', compact('bookings'));
    }

    /** نموذج تأكيد الحجز */
    public function create(Slot $slot)
    {
        abort_if($slot->is_booked, 404, 'هذا الوقت محجوز مسبقاً.');
        abort_if($slot->start_at->isPast(), 404, 'انتهى وقت هذا الموعد.');

        $slot->load('service.provider');

        return view('bookings.create', compact('slot'));
    }

    /** حفظ الحجز */
    public function store(Request $request)
    {
        $data = $request->validate([
            'slot_id' => 'required|exists:slots,id',
            'notes'   => 'nullable|string|max:500',
        ]);

        $slot = Slot::findOrFail($data['slot_id']);

        abort_if($slot->is_booked, 422, 'هذا الوقت محجوز مسبقاً.');
        abort_if($slot->start_at->isPast(), 422, 'انتهى وقت هذا الموعد.');

        $booking = DB::transaction(function () use ($slot, $data) {
            $slot->update(['is_booked' => true]);

            return Booking::create([
                'customer_id' => auth()->id(),
                'slot_id'     => $slot->id,
                'status'      => 'pending',
                'notes'       => $data['notes'] ?? null,
            ]);
        });

        // إشعار العميل ومزود الخدمة
        $booking->load('slot.service.provider');
        $booking->customer->notify(new BookingCreated($booking));
        $booking->slot->service->provider->notify(new BookingCreated($booking));

        if ($booking->slot->service->price > 0) {
            return redirect()->route('bookings.payment.create', $booking)
                ->with('success', 'تم الحجز بنجاح! أكمل الدفع لتأكيد حجزك.');
        }

        return redirect()->route('bookings.show', $booking)
            ->with('success', 'تم الحجز بنجاح! سيتم التأكيد قريباً.');
    }

    /** عرض تفاصيل الحجز */
    public function show(Booking $booking)
    {
        $this->authorize('view', $booking);
        $booking->load(['slot.service.provider', 'review', 'payment']);

        return view('bookings.show', compact('booking'));
    }

    /** إلغاء الحجز */
    public function cancel(Request $request, Booking $booking)
    {
        $this->authorize('cancel', $booking);
        abort_unless($booking->canBeCancelled(), 422, 'لا يمكن إلغاء هذا الحجز.');

        $request->validate([
            'cancellation_reason' => 'nullable|string|max:300',
        ]);

        DB::transaction(function () use ($booking, $request) {
            $booking->slot->update(['is_booked' => false]);
            $booking->update([
                'status'              => 'cancelled',
                'cancelled_at'        => now(),
                'cancellation_reason' => $request->cancellation_reason,
            ]);
        });

        $booking->load('slot.service.provider');
        $booking->customer->notify(new BookingCancelled($booking));
        $booking->slot->service->provider->notify(new BookingCancelled($booking));

        return redirect()->route('bookings.index')
            ->with('success', 'تم إلغاء الحجز.');
    }
}
