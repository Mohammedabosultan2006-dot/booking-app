@extends('layouts.app')
@section('title', 'تأكيد الحجز')

@section('content')
<div class="max-w-xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">تأكيد الحجز</h1>

    <!-- Summary -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <h2 class="font-semibold text-gray-900 text-lg mb-4">{{ $slot->service->name }}</h2>

        <div class="space-y-3 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-500">المزود</span>
                <span class="font-medium text-gray-800">{{ $slot->service->provider->name }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">التاريخ</span>
                <span class="font-medium text-gray-800">
                    {{ $slot->start_at->locale('ar')->isoFormat('dddd، D MMMM YYYY') }}
                </span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">الوقت</span>
                <span class="font-medium text-gray-800">
                    {{ $slot->start_at->format('H:i') }} - {{ $slot->end_at->format('H:i') }}
                </span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">المدة</span>
                <span class="font-medium text-gray-800">{{ $slot->service->duration_minutes }} دقيقة</span>
            </div>
            @if($slot->service->location)
            <div class="flex justify-between">
                <span class="text-gray-500">الموقع</span>
                <span class="font-medium text-gray-800">{{ $slot->service->location }}</span>
            </div>
            @endif
            <div class="flex justify-between border-t border-gray-100 pt-3 mt-1">
                <span class="text-gray-700 font-semibold">السعر</span>
                <span class="font-bold text-indigo-600 text-base">{{ number_format($slot->service->price, 0) }} ₪</span>
            </div>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('bookings.store') }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        @csrf
        <input type="hidden" name="slot_id" value="{{ $slot->id }}">

        <div class="mb-5">
            <label class="block text-sm font-medium text-gray-700 mb-2">ملاحظات (اختياري)</label>
            <textarea name="notes" rows="3" placeholder="أي ملاحظات أو طلبات خاصة..."
                class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 resize-none">{{ old('notes') }}</textarea>
        </div>

        <div class="flex gap-3">
            <button type="submit"
                class="flex-1 bg-indigo-600 text-white py-3 rounded-xl font-semibold hover:bg-indigo-700 transition-colors">
                <i class="fa-solid fa-calendar-check ml-2"></i>تأكيد الحجز
            </button>
            <a href="{{ route('services.show', $slot->service) }}"
               class="px-5 py-3 border border-gray-200 rounded-xl text-gray-600 hover:bg-gray-50 transition-colors text-sm flex items-center">
                إلغاء
            </a>
        </div>
    </form>
</div>
@endsection
