<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{

    public function store(Request $request, Booking $booking)
    {
        abort_unless($booking->customer_id === auth()->id(), 403);
        abort_unless($booking->isCompleted(), 422, 'يمكن التقييم فقط بعد اكتمال الموعد.');
        abort_if($booking->review()->exists(), 422, 'لقد قمت بتقييم هذا الحجز مسبقاً.');

        $data = $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:600',
        ]);

        Review::create([
            'booking_id'  => $booking->id,
            'customer_id' => auth()->id(),
            'service_id'  => $booking->slot->service_id,
            'rating'      => $data['rating'],
            'comment'     => $data['comment'] ?? null,
        ]);

        return back()->with('success', 'شكراً على تقييمك!');
    }
}
