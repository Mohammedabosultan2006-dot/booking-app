<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingCancelled extends Notification
{
    use Queueable;

    public function __construct(public Booking $booking) {}

    public function via($notifiable): array { return ['mail', 'database']; }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('❌ تم إلغاء الحجز - ' . $this->booking->slot->service->name)
            ->greeting('مرحباً ' . $notifiable->name)
            ->line('تم إلغاء الحجز.')
            ->when($this->booking->cancellation_reason, fn($mail) =>
                $mail->line('**السبب:** ' . $this->booking->cancellation_reason)
            )
            ->action('حجز موعد جديد', route('services.index'))
            ->line('نعتذر عن أي إزعاج.');
    }

    public function toArray($notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'message'    => 'تم إلغاء حجز: ' . $this->booking->slot->service->name,
        ];
    }
}
