<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\PaymentMethod;
use App\Models\Service;
use App\Models\Slot;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PaymentFlowTest extends TestCase
{
    use RefreshDatabase;

    private function paidBooking(): Booking
    {
        $provider = User::factory()->create(['role' => 'provider']);
        $service = Service::factory()->create(['provider_id' => $provider->id, 'price' => 100]);
        $slot = Slot::factory()->for($service)->create(['is_booked' => true]);
        $customer = User::factory()->create(['role' => 'customer']);

        return Booking::create([
            'customer_id' => $customer->id,
            'slot_id'     => $slot->id,
            'status'      => 'pending',
        ]);
    }

    public function test_customer_can_submit_payment_proof_for_their_own_booking(): void
    {
        Notification::fake();

        $booking = $this->paidBooking();
        $method = PaymentMethod::factory()->create(['provider_id' => $booking->slot->service->provider_id]);
        $proof = UploadedFile::fake()->image('proof.jpg');

        $response = $this->actingAs($booking->customer)->post("/bookings/{$booking->id}/payment", [
            'payment_method_id' => $method->id,
            'reference_number'  => 'TX-123',
            'proof'             => $proof,
        ]);

        $response->assertRedirect(route('bookings.show', $booking));
        $this->assertDatabaseHas('payments', [
            'booking_id' => $booking->id,
            'status'     => 'submitted',
            'amount'     => 100,
        ]);
    }

    public function test_a_different_customer_cannot_pay_for_someone_elses_booking(): void
    {
        $booking = $this->paidBooking();
        $stranger = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($stranger)->get("/bookings/{$booking->id}/payment");

        $response->assertForbidden();
    }

    public function test_provider_cannot_confirm_a_paid_booking_before_payment_is_verified(): void
    {
        $booking = $this->paidBooking();
        $provider = $booking->slot->service->provider;

        $response = $this->actingAs($provider)->patch("/provider/bookings/{$booking->id}/confirm");

        $response->assertStatus(422);
        $this->assertSame('pending', $booking->fresh()->status);
    }

    public function test_provider_can_confirm_once_payment_is_verified(): void
    {
        $booking = $this->paidBooking();
        $provider = $booking->slot->service->provider;

        \App\Models\Payment::create([
            'booking_id'   => $booking->id,
            'customer_id'  => $booking->customer_id,
            'provider_id'  => $provider->id,
            'amount'       => 100,
            'method'       => 'bank',
            'status'       => 'verified',
            'verified_at'  => now(),
        ]);

        $response = $this->actingAs($provider)->patch("/provider/bookings/{$booking->id}/confirm");

        $response->assertRedirect();
        $this->assertSame('confirmed', $booking->fresh()->status);
    }
}
