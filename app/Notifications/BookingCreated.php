<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingCreated extends Notification
{
    use Queueable;

    public function __construct(public Booking $booking) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $service = $this->booking->slot->service;
        $slot    = $this->booking->slot;

        return (new MailMessage)
            ->subject('تم استلام حجزك - ' . $service->name)
            ->greeting('مرحباً ' . $notifiable->name)
            ->line('تم استلام حجزك بنجاح وهو قيد المراجعة.')
            ->line('**الخدمة:** ' . $service->name)
            ->line('**التاريخ:** ' . $slot->start_at->format('Y-m-d'))
            ->line('**الوقت:** ' . $slot->start_at->format('H:i') . ' - ' . $slot->end_at->format('H:i'))
            ->action('عرض الحجز', route('bookings.show', $this->booking))
            ->line('شكراً لاستخدامك خدماتنا!');
    }

    public function toArray($notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'message'    => 'حجز جديد: ' . $this->booking->slot->service->name,
        ];
    }
}
