<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Service;
use App\Models\Slot;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_book_a_free_service_and_is_confirmed_pending(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $service = Service::factory()->create(['price' => 0]);
        $slot = Slot::factory()->for($service)->create();

        $response = $this->actingAs($customer)->post('/bookings', [
            'slot_id' => $slot->id,
        ]);

        $response->assertRedirect(route('bookings.show', Booking::first()));
        $this->assertDatabaseHas('bookings', [
            'customer_id' => $customer->id,
            'slot_id'     => $slot->id,
            'status'      => 'pending',
        ]);
        $this->assertTrue($slot->fresh()->is_booked);
    }

    public function test_booking_a_paid_service_redirects_to_the_payment_page(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $service = Service::factory()->create(['price' => 100]);
        $slot = Slot::factory()->for($service)->create();

        $response = $this->actingAs($customer)->post('/bookings', [
            'slot_id' => $slot->id,
        ]);

        $booking = Booking::first();
        $response->assertRedirect(route('bookings.payment.create', $booking));
    }

    public function test_an_already_booked_slot_cannot_be_booked_again(): void
    {
        $service = Service::factory()->create();
        $slot = Slot::factory()->for($service)->create(['is_booked' => true]);
        $customer = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($customer)->post('/bookings', [
            'slot_id' => $slot->id,
        ]);

        $response->assertStatus(422);
        $this->assertDatabaseCount('bookings', 0);
    }

    public function test_cancelling_a_booking_frees_the_slot(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $service = Service::factory()->create();
        $slot = Slot::factory()->for($service)->create([
            'is_booked' => true,
            'start_at'  => now()->addDays(2),
            'end_at'    => now()->addDays(2)->addMinutes(30),
        ]);
        $booking = Booking::create([
            'customer_id' => $customer->id,
            'slot_id'     => $slot->id,
            'status'      => 'pending',
        ]);

        $response = $this->actingAs($customer)->patch("/bookings/{$booking->id}/cancel");

        $response->assertRedirect(route('bookings.index'));
        $this->assertSame('cancelled', $booking->fresh()->status);
        $this->assertFalse($slot->fresh()->is_booked);
    }
}
