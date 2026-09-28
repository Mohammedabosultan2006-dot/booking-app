<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function view(User $user, Booking $booking): bool
    {
        return $user->id === $booking->customer_id
            || $user->id === $booking->slot->service->provider_id
            || $user->isAdmin();
    }

    public function cancel(User $user, Booking $booking): bool
    {
        return $user->id === $booking->customer_id || $user->isAdmin();
    }

    public function manage(User $user, Booking $booking): bool
    {
        return $user->id === $booking->slot->service->provider_id || $user->isAdmin();
    }
}
