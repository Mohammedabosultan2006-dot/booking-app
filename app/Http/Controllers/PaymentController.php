<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Notifications\PaymentSubmitted;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /** صفحة الدفع */
    public function create(Booking $booking)
    {
        abort_unless($booking->customer_id === auth()->id(), 403);

        $booking->load(['slot.service.provider', 'payment']);
        abort_if($booking->isCancelled(), 422, 'تم إلغاء هذا الحجز.');

        $service = $booking->slot->service;
        $paymentMethods = $service->provider->paymentMethods()->where('is_active', true)->get();

        return view('payments.create', compact('booking', 'service', 'paymentMethods'));
    }

    /** رفع إثبات الدفع */
    public function store(Request $request, Booking $booking)
    {
        abort_unless($booking->customer_id === auth()->id(), 403);

        $booking->load('slot.service.provider');
        abort_if($booking->isCancelled(), 422, 'تم إلغاء هذا الحجز.');

        $service = $booking->slot->service;

        $data = $request->validate([
            'payment_method_id' => 'required|exists:payment_methods,id,provider_id,' . $service->provider_id,
            'reference_number'  => 'required|string|max:100',
            'proof'             => 'required|image|max:4096',
        ]);

        $proofPath = $request->file('proof')->store('payment_proofs', 'public');

        $payment = Payment::updateOrCreate(
            ['booking_id' => $booking->id],
            [
                'customer_id'        => auth()->id(),
                'provider_id'        => $service->provider_id,
                'payment_method_id'  => $data['payment_method_id'],
                'amount'             => $service->price,
                'method'             => PaymentMethod::findOrFail($data['payment_method_id'])->type,
                'status'             => 'submitted',
                'reference_number'   => $data['reference_number'],
                'proof_path'         => $proofPath,
                'submitted_at'       => now(),
                'rejected_reason'    => null,
            ]
        );

        $payment->provider->notify(new PaymentSubmitted($payment));

        return redirect()->route('bookings.show', $booking)
            ->with('success', 'تم رفع إثبات الدفع، بانتظار مراجعة مزود الخدمة.');
    }
}
