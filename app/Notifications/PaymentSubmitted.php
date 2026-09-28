<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentSubmitted extends Notification
{
    use Queueable;

    public function __construct(public Payment $payment) {}

    public function via($notifiable): array { return ['mail', 'database']; }

    public function toMail($notifiable): MailMessage
    {
        $service = $this->payment->booking->slot->service;

        return (new MailMessage)
            ->subject('💰 إثبات دفع جديد بانتظار المراجعة')
            ->greeting('مرحباً ' . $notifiable->name)
            ->line('قام العميل ' . $this->payment->customer->name . ' برفع إثبات دفع للخدمة "' . $service->name . '".')
            ->line('**المبلغ:** ' . number_format($this->payment->amount, 0) . ' ₪')
            ->line('**الوسيلة:** ' . $this->payment->paymentMethod?->type_label)
            ->action('مراجعة الدفعة', route('provider.bookings.index'))
            ->line('يرجى مراجعة الإثبات وتوثيق الدفعة لتتمكن من تأكيد الحجز.');
    }

    public function toArray($notifiable): array
    {
        return [
            'payment_id' => $this->payment->id,
            'booking_id' => $this->payment->booking_id,
            'message'    => 'إثبات دفع جديد بانتظار المراجعة: ' . $this->payment->booking->slot->service->name,
        ];
    }
}
