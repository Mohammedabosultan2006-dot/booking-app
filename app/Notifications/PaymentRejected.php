<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentRejected extends Notification
{
    use Queueable;

    public function __construct(public Payment $payment) {}

    public function via($notifiable): array { return ['mail', 'database']; }

    public function toMail($notifiable): MailMessage
    {
        $service = $this->payment->booking->slot->service;

        return (new MailMessage)
            ->subject('⚠️ تم رفض إثبات الدفع - ' . $service->name)
            ->greeting('مرحباً ' . $notifiable->name)
            ->line('للأسف تم رفض إثبات الدفع الذي رفعته.')
            ->when($this->payment->rejected_reason, fn($mail) =>
                $mail->line('**السبب:** ' . $this->payment->rejected_reason)
            )
            ->line('يرجى إعادة المحاولة برفع إثبات دفع صحيح.')
            ->action('إعادة المحاولة', route('bookings.payment.create', $this->payment->booking_id));
    }

    public function toArray($notifiable): array
    {
        return [
            'payment_id' => $this->payment->id,
            'booking_id' => $this->payment->booking_id,
            'message'    => 'تم رفض إثبات الدفع: ' . $this->payment->booking->slot->service->name,
        ];
    }
}
