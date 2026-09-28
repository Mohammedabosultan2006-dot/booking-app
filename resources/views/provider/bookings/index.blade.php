@extends('layouts.provider')
@section('title', 'حجوزات العملاء')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <h1 class="text-2xl font-bold text-gray-900">حجوزات العملاء</h1>
    <a href="{{ route('provider.services.index') }}" class="text-sm text-emerald-600 hover:underline">
        <i class="fa-solid fa-arrow-right ml-1"></i>خدماتي
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    @if($bookings->isEmpty())
        <div class="text-center py-16 text-gray-400">
            <i class="fa-solid fa-calendar-xmark text-4xl mb-3"></i>
            <p>لا توجد حجوزات بعد</p>
        </div>
    @else
    <div class="overflow-x-auto">
    <table class="w-full text-sm min-w-[720px]">
        <thead class="bg-gray-50 border-b border-gray-100">
            <tr>
                <th class="text-right px-5 py-3 text-gray-500 font-medium">العميل</th>
                <th class="text-right px-5 py-3 text-gray-500 font-medium">الخدمة</th>
                <th class="text-right px-5 py-3 text-gray-500 font-medium">الموعد</th>
                <th class="text-right px-5 py-3 text-gray-500 font-medium">الحالة</th>
                <th class="text-right px-5 py-3 text-gray-500 font-medium">الدفع</th>
                <th class="text-right px-5 py-3 text-gray-500 font-medium">إجراءات</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($bookings as $booking)
            @php $payment = $booking->payment; @endphp
            <tr class="hover:bg-gray-50" x-data="{ showProof: false }">
                <td class="px-5 py-4">
                    <div class="flex items-center gap-2">
                        <img src="{{ $booking->customer->avatar_url }}" class="w-7 h-7 rounded-full object-cover">
                        <div>
                            <p class="font-medium text-gray-900">{{ $booking->customer->name }}</p>
                            <p class="text-xs text-gray-400">{{ $booking->customer->phone }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-5 py-4 text-gray-700">{{ $booking->slot->service->name }}</td>
                <td class="px-5 py-4 text-gray-600">
                    {{ $booking->slot->start_at->format('Y/m/d') }}<br>
                    <span class="text-xs text-gray-400">{{ $booking->slot->start_at->format('H:i') }}</span>
                </td>
                <td class="px-5 py-4">
                    <span class="text-xs px-2 py-1 rounded-full font-medium
                        @if($booking->isPending()) bg-yellow-100 text-yellow-800
                        @elseif($booking->isConfirmed()) bg-green-100 text-green-800
                        @elseif($booking->isCancelled()) bg-red-100 text-red-800
                        @else bg-blue-100 text-blue-800 @endif">
                        @switch($booking->status)
                            @case('pending')   انتظار @break
                            @case('confirmed') مؤكد    @break
                            @case('cancelled') ملغي     @break
                            @case('completed') مكتمل   @break
                        @endswitch
                    </span>
                </td>
                <td class="px-5 py-4">
                    @if($booking->slot->service->price <= 0)
                        <span class="text-xs text-gray-400">مجانية</span>
                    @elseif(!$payment || $payment->isPending())
                        <span class="text-xs px-2 py-1 rounded-full font-medium pay-pending">لم يدفع بعد</span>
                    @elseif($payment->isSubmitted())
                        <button @click="showProof = true" class="text-xs px-2 py-1 rounded-full font-medium pay-submitted hover:opacity-80">
                            <i class="fa-solid fa-hourglass-half ml-1"></i>بانتظار المراجعة
                        </button>
                    @elseif($payment->isVerified())
                        <span class="text-xs px-2 py-1 rounded-full font-medium pay-verified">
                            <i class="fa-solid fa-circle-check ml-1"></i>موثّق
                        </span>
                    @elseif($payment->isRejected())
                        <span class="text-xs px-2 py-1 rounded-full font-medium pay-rejected">مرفوض</span>
                    @endif

                    @if($payment && $payment->isSubmitted())
                    <div x-show="showProof" x-cloak
                         class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
                         @click.self="showProof = false">
                        <div class="bg-white rounded-2xl p-6 max-w-sm w-full">
                            <h3 class="font-semibold text-gray-900 mb-3">مراجعة إثبات الدفع</h3>
                            <div class="space-y-2 text-sm mb-4">
                                <div class="flex justify-between"><span class="text-gray-500">المبلغ</span><span class="font-bold">{{ number_format($payment->amount, 0) }} ₪</span></div>
                                <div class="flex justify-between"><span class="text-gray-500">الوسيلة</span><span>{{ $payment->paymentMethod?->type_label }}</span></div>
                                <div class="flex justify-between"><span class="text-gray-500">الرقم المرجعي</span><span>{{ $payment->reference_number }}</span></div>
                            </div>
                            <a href="{{ $payment->proof_url }}" target="_blank">
                                <img src="{{ $payment->proof_url }}" class="w-full rounded-xl border border-gray-100 mb-4 max-h-64 object-cover">
                            </a>
                            <div class="flex gap-2">
                                <form action="{{ route('provider.payments.verify', $payment) }}" method="POST" class="flex-1">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="w-full text-sm bg-emerald-500 text-white py-2 rounded-xl hover:bg-emerald-600">
                                        <i class="fa-solid fa-check ml-1"></i>توثيق
                                    </button>
                                </form>
                                <form action="{{ route('provider.payments.reject', $payment) }}" method="POST" class="flex-1"
                                      onsubmit="return confirm('رفض إثبات الدفع؟')">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="w-full text-sm border border-red-200 text-red-600 py-2 rounded-xl hover:bg-red-50">
                                        رفض
                                    </button>
                                </form>
                            </div>
                            <button @click="showProof = false" class="w-full text-center text-xs text-gray-400 mt-3">إغلاق</button>
                        </div>
                    </div>
                    @endif
                </td>
                <td class="px-5 py-4">
                    <div class="flex gap-2">
                        @if($booking->isPending())
                            @if($booking->slot->service->price > 0 && !$payment?->isVerified())
                                <span class="text-xs text-gray-400" title="بانتظار توثيق الدفع">
                                    <i class="fa-solid fa-lock ml-1"></i>بانتظار الدفع
                                </span>
                            @else
                                <form action="{{ route('provider.bookings.confirm', $booking) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="text-xs bg-green-500 text-white px-3 py-1.5 rounded-lg hover:bg-green-600">تأكيد</button>
                                </form>
                            @endif
                        @endif
                        @if($booking->isConfirmed())
                        <form action="{{ route('provider.bookings.complete', $booking) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" class="text-xs bg-blue-500 text-white px-3 py-1.5 rounded-lg hover:bg-blue-600">مكتمل</button>
                        </form>
                        @endif
                        @if($booking->canBeCancelled())
                        <form action="{{ route('provider.bookings.cancel', $booking) }}" method="POST"
                              onsubmit="return confirm('إلغاء هذا الحجز؟')">
                            @csrf @method('PATCH')
                            <button type="submit" class="text-xs border border-red-200 text-red-600 px-3 py-1.5 rounded-lg hover:bg-red-50">إلغاء</button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>
    <div class="p-4">{{ $bookings->links() }}</div>
    @endif
</div>
@endsection
