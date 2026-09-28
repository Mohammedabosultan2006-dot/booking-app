<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Notifications\PaymentRejected;
use App\Notifications\PaymentVerified;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function verify(Payment $payment)
    {
        abort_unless($payment->provider_id === auth()->id(), 403);
        abort_unless($payment->isSubmitted(), 422, 'هذه الدفعة ليست بانتظار المراجعة.');

        $payment->update(['status' => 'verified', 'verified_at' => now()]);
        $payment->customer->notify(new PaymentVerified($payment));

        return back()->with('success', 'تم توثيق الدفعة بنجاح.');
    }

    public function reject(Request $request, Payment $payment)
    {
        abort_unless($payment->provider_id === auth()->id(), 403);
        abort_unless($payment->isSubmitted(), 422, 'هذه الدفعة ليست بانتظار المراجعة.');

        $request->validate(['reason' => 'nullable|string|max:300']);

        $payment->update([
            'status'           => 'rejected',
            'rejected_reason'  => $request->reason,
        ]);
        $payment->customer->notify(new PaymentRejected($payment));

        return back()->with('success', 'تم رفض إثبات الدفع.');
    }
}
