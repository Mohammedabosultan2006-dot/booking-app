@extends('layouts.provider')
@section('title', 'الرئيسية')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-bold text-gray-900">أهلاً، {{ auth()->user()->name }} 👋</h1>
    <p class="text-gray-500">نظرة عامة على نشاطك اليوم</p>
</div>

<!-- Stats -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    @php
    $cards = [
        ['label'=>'حجوزات اليوم',   'value'=>$stats['today_bookings'],  'icon'=>'fa-calendar-day',   'color'=>'text-blue-500',    'bg'=>'bg-blue-50'],
        ['label'=>'بانتظار التأكيد', 'value'=>$stats['pending_bookings'], 'icon'=>'fa-hourglass-half', 'color'=>'text-amber-500',   'bg'=>'bg-amber-50'],
        ['label'=>'دفعات للمراجعة', 'value'=>$stats['pending_payments'], 'icon'=>'fa-wallet',          'color'=>'text-rose-500',    'bg'=>'bg-rose-50'],
        ['label'=>'إيراد الشهر',    'value'=>number_format($stats['month_revenue'],0).' ₪', 'icon'=>'fa-coins', 'color'=>'text-emerald-500', 'bg'=>'bg-emerald-50'],
    ];
    @endphp
    @foreach($cards as $card)
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs text-gray-400 font-medium">{{ $card['label'] }}</span>
            <div class="w-9 h-9 {{ $card['bg'] }} rounded-xl flex items-center justify-center">
                <i class="fa-solid {{ $card['icon'] }} {{ $card['color'] }} text-sm"></i>
            </div>
        </div>
        <p class="text-2xl font-bold text-gray-900">{{ $card['value'] }}</p>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
    <!-- Recent Bookings -->
    <div class="lg:col-span-3 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-semibold text-gray-900">آخر الحجوزات</h2>
            <a href="{{ route('provider.bookings.index') }}" class="text-xs text-emerald-600 hover:underline">عرض الكل</a>
        </div>
        <div class="divide-y divide-gray-100">
            @forelse($recentBookings as $booking)
            <div class="px-5 py-3 flex items-center gap-3">
                <img src="{{ $booking->customer->avatar_url }}" class="w-8 h-8 rounded-full object-cover flex-shrink-0">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-800 truncate">{{ $booking->customer->name }}</p>
                    <p class="text-xs text-gray-400 truncate">{{ $booking->slot->service->name }}</p>
                </div>
                <span class="text-xs px-2 py-1 rounded-full font-medium flex-shrink-0
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
            </div>
            @empty
            <div class="text-center py-12 text-gray-400">لا توجد حجوزات بعد</div>
            @endforelse
        </div>
    </div>

    <!-- Pending Payments + Quick Links -->
    <div class="lg:col-span-2 space-y-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h3 class="font-semibold text-gray-900 mb-4">
                <i class="fa-solid fa-wallet text-rose-500 ml-1"></i>دفعات بانتظار المراجعة
            </h3>
            @forelse($pendingPayments as $payment)
            <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                <div class="min-w-0">
                    <p class="text-sm font-medium text-gray-800 truncate">{{ $payment->customer->name }}</p>
                    <p class="text-xs text-gray-400 truncate">{{ $payment->booking->slot->service->name }}</p>
                </div>
                <span class="text-sm font-bold text-gray-700 flex-shrink-0">{{ number_format($payment->amount,0) }} ₪</span>
            </div>
            @empty
            <p class="text-sm text-gray-400 text-center py-6">لا توجد دفعات بانتظار المراجعة</p>
            @endforelse
            @if($pendingPayments->isNotEmpty())
            <a href="{{ route('provider.bookings.index') }}" class="block text-center text-xs text-emerald-600 hover:underline mt-3">مراجعة الدفعات</a>
            @endif
        </div>

        <div class="bg-slate-900 rounded-2xl p-5 text-white">
            <div class="flex items-center justify-between mb-1">
                <span class="text-slate-400 text-xs">متوسط التقييم</span>
                <i class="fa-solid fa-star text-amber-400"></i>
            </div>
            <p class="text-2xl font-bold">{{ $stats['avg_rating'] }} <span class="text-sm font-normal text-slate-400">/ 5</span></p>
            <p class="text-slate-400 text-xs mt-1">عبر {{ $stats['total_services'] }} خدمة</p>
        </div>

        <a href="{{ route('provider.services.create') }}"
           class="block bg-emerald-500 text-white text-center py-3 rounded-xl text-sm font-semibold hover:bg-emerald-600 transition-colors">
            <i class="fa-solid fa-plus ml-2"></i>إضافة خدمة جديدة
        </a>
    </div>
</div>
@endsection
