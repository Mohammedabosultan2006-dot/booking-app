@extends('layouts.app')
@section('title', 'تفاصيل الحجز')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('bookings.index') }}" class="text-gray-400 hover:text-gray-600">
            <i class="fa-solid fa-arrow-right"></i>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">تفاصيل الحجز</h1>
    </div>

    @php $service = $booking->slot->service; $slot = $booking->slot; $payment = $booking->payment; @endphp

    @if($service->price > 0)
        @if(!$payment || $payment->isPending())
            <div class="bg-indigo-50 border border-indigo-100 rounded-2xl p-5 mb-5 flex items-center justify-between gap-4">
                <div>
                    <p class="font-semibold text-indigo-900">أكمل الدفع لتأكيد حجزك</p>
                    <p class="text-sm text-indigo-600">المبلغ المطلوب: {{ number_format($service->price, 0) }} ₪</p>
                </div>
                <a href="{{ route('bookings.payment.create', $booking) }}"
                   class="bg-indigo-600 text-white text-sm font-semibold px-5 py-2.5 rounded-xl hover:bg-indigo-700 flex-shrink-0">
                    ادفع الآن
                </a>
            </div>
        @elseif($payment->isSubmitted())
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 mb-5">
                <p class="font-semibold text-amber-800"><i class="fa-solid fa-hourglass-half ml-1"></i>بانتظار مراجعة مزود الخدمة للدفع</p>
                <p class="text-sm text-amber-700 mt-1">تم رفع إثبات الدفع، سيتم إشعارك عند المراجعة.</p>
            </div>
        @elseif($payment->isVerified())
            <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-5 mb-5">
                <p class="font-semibold text-emerald-800"><i class="fa-solid fa-circle-check ml-1"></i>تم تأكيد الدفع</p>
            </div>
        @elseif($payment->isRejected())
            <div class="bg-red-50 border border-red-200 rounded-2xl p-5 mb-5">
                <p class="font-semibold text-red-800"><i class="fa-solid fa-circle-xmark ml-1"></i>تم رفض إثبات الدفع</p>
                @if($payment->rejected_reason)
                    <p class="text-sm text-red-700 mt-1">السبب: {{ $payment->rejected_reason }}</p>
                @endif
                <a href="{{ route('bookings.payment.create', $booking) }}"
                   class="inline-block mt-3 bg-red-600 text-white text-sm font-semibold px-5 py-2 rounded-xl hover:bg-red-700">
                    إعادة المحاولة
                </a>
            </div>
        @endif
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-5">
        <!-- Status Badge -->
        <div class="flex items-center justify-between mb-5">
            <h2 class="font-semibold text-gray-900 text-lg">{{ $service->name }}</h2>
            <span class="text-sm px-3 py-1 rounded-full font-medium
                @if($booking->isPending()) bg-yellow-100 text-yellow-800
                @elseif($booking->isConfirmed()) bg-green-100 text-green-800
                @elseif($booking->isCancelled()) bg-red-100 text-red-800
                @else bg-blue-100 text-blue-800 @endif">
                @switch($booking->status)
                    @case('pending')   قيد الانتظار @break
                    @case('confirmed') مؤكد          @break
                    @case('cancelled') ملغي           @break
                    @case('completed') مكتمل          @break
                @endswitch
            </span>
        </div>

        <div class="space-y-3 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-500">مزود الخدمة</span>
                <span class="font-medium">{{ $service->provider->name }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">التاريخ</span>
                <span class="font-medium">{{ $slot->start_at->locale('ar')->isoFormat('dddd، D MMMM YYYY') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">الوقت</span>
                <span class="font-medium">{{ $slot->start_at->format('H:i') }} - {{ $slot->end_at->format('H:i') }}</span>
            </div>
            @if($service->location)
            <div class="flex justify-between">
                <span class="text-gray-500">الموقع</span>
                <span class="font-medium">{{ $service->location }}</span>
            </div>
            @endif
            <div class="flex justify-between">
                <span class="text-gray-500">السعر</span>
                <span class="font-bold text-indigo-600">{{ number_format($service->price, 0) }} ₪</span>
            </div>
            @if($booking->notes)
            <div class="pt-3 border-t border-gray-100">
                <span class="text-gray-500 block mb-1">ملاحظاتك</span>
                <p class="text-gray-700">{{ $booking->notes }}</p>
            </div>
            @endif
            @if($booking->isCancelled() && $booking->cancellation_reason)
            <div class="pt-3 border-t border-gray-100">
                <span class="text-red-500 block mb-1">سبب الإلغاء</span>
                <p class="text-gray-700">{{ $booking->cancellation_reason }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Cancel Button -->
    @if($booking->canBeCancelled())
    <form action="{{ route('bookings.cancel', $booking) }}" method="POST"
          onsubmit="return confirm('هل أنت متأكد من إلغاء الحجز؟')">
        @csrf @method('PATCH')
        <div class="mb-3">
            <textarea name="cancellation_reason" placeholder="سبب الإلغاء (اختياري)" rows="2"
                class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-200 resize-none"></textarea>
        </div>
        <button type="submit"
            class="w-full border border-red-200 text-red-600 py-3 rounded-xl hover:bg-red-50 transition-colors text-sm font-medium">
            <i class="fa-solid fa-xmark ml-2"></i>إلغاء الحجز
        </button>
    </form>
    @endif

    <!-- Review Form -->
    @if($booking->isCompleted() && !$booking->review)
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mt-5">
        <h3 class="font-semibold text-gray-900 mb-4">قيّم الخدمة</h3>
        <form action="{{ route('reviews.store', $booking) }}" method="POST">
            @csrf
            <div class="flex gap-2 mb-4" id="stars">
                @for($i=1;$i<=5;$i++)
                <label class="cursor-pointer">
                    <input type="radio" name="rating" value="{{ $i }}" class="sr-only" required>
                    <i class="fa-regular fa-star text-2xl text-amber-400 hover:fa-solid transition-all star-icon" data-value="{{ $i }}"></i>
                </label>
                @endfor
            </div>
            <textarea name="comment" rows="3" placeholder="أخبرنا عن تجربتك..."
                class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-200 resize-none mb-3"></textarea>
            <button type="submit" class="w-full bg-amber-400 text-white py-3 rounded-xl font-semibold hover:bg-amber-500 transition-colors">
                <i class="fa-solid fa-star ml-1"></i>إرسال التقييم
            </button>
        </form>
    </div>
    @elseif($booking->review)
    <div class="bg-amber-50 rounded-2xl p-4 mt-5 text-center">
        <i class="fa-solid fa-star text-amber-400 text-2xl mb-1"></i>
        <p class="text-sm text-amber-800 font-medium">لقد قمت بتقييم هذه الخدمة — شكراً!</p>
    </div>
    @endif
</div>
@endsection
