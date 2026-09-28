@extends('layouts.app')
@section('title', 'حجوزاتي')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <h1 class="text-2xl font-bold text-gray-900">حجوزاتي</h1>
    <a href="{{ route('services.index') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-xl text-sm hover:bg-indigo-700">
        <i class="fa-solid fa-plus ml-1"></i>حجز جديد
    </a>
</div>

@if($bookings->isEmpty())
    <div class="text-center py-20 text-gray-400">
        <i class="fa-solid fa-calendar-xmark text-5xl mb-4"></i>
        <p class="mb-4">لا توجد حجوزات بعد</p>
        <a href="{{ route('services.index') }}" class="text-indigo-600 hover:underline">تصفح الخدمات</a>
    </div>
@else
    <div class="space-y-4">
        @foreach($bookings as $booking)
        @php $service = $booking->slot->service; $slot = $booking->slot; @endphp
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex flex-col sm:flex-row sm:items-center gap-4">
            <div class="flex items-center gap-4 flex-1 min-w-0">
                <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-calendar text-indigo-500 text-xl"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-semibold text-gray-900">{{ $service->name }}</h3>
                    <p class="text-sm text-gray-500">
                        {{ $slot->start_at->locale('ar')->isoFormat('dddd، D MMMM YYYY') }}
                        — {{ $slot->start_at->format('H:i') }} - {{ $slot->end_at->format('H:i') }}
                    </p>
                    <p class="text-xs text-gray-400">{{ $service->provider->name }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3 flex-shrink-0">
                <span class="text-xs px-3 py-1 rounded-full font-medium
                    @if($booking->isPending()) bg-yellow-100 text-yellow-800
                    @elseif($booking->isConfirmed()) bg-green-100 text-green-800
                    @elseif($booking->isCancelled()) bg-red-100 text-red-800
                    @else bg-blue-100 text-blue-800 @endif">
                    @switch($booking->status)
                        @case('pending')    قيد الانتظار @break
                        @case('confirmed')  مؤكد         @break
                        @case('cancelled')  ملغي          @break
                        @case('completed')  مكتمل         @break
                    @endswitch
                </span>
                <a href="{{ route('bookings.show', $booking) }}" class="text-indigo-600 text-sm hover:underline">
                    تفاصيل
                </a>
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-6">{{ $bookings->links() }}</div>
@endif
@endsection
