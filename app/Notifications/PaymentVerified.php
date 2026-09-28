<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentVerified extends Notification
{
    use Queueable;

    public function __construct(public Payment $payment) {}

    public function via($notifiable): array { return ['mail', 'database']; }

    public function toMail($notifiable): MailMessage
    {
        $service = $this->payment->booking->slot->service;

        return (new MailMessage)
            ->subject('✅ تم توثيق دفعتك - ' . $service->name)
            ->greeting('مرحباً ' . $notifiable->name)
            ->line('تم التحقق من دفعتك بنجاح، وسيقوم مزود الخدمة بتأكيد حجزك قريباً.')
            ->line('**الخدمة:** ' . $service->name)
            ->line('**المبلغ:** ' . number_format($this->payment->amount, 0) . ' ₪')
            ->action('عرض الحجز', route('bookings.show', $this->payment->booking_id));
    }

    public function toArray($notifiable): array
    {
        return [
            'payment_id' => $this->payment->id,
            'booking_id' => $this->payment->booking_id,
            'message'    => 'تم توثيق دفعتك: ' . $this->payment->booking->slot->service->name,
        ];
    }
}
