@extends('layouts.provider')
@section('title', 'خدماتي')

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <h1 class="text-2xl font-bold text-gray-900">خدماتي</h1>
    <div class="flex gap-3">
        <a href="{{ route('provider.bookings.index') }}" class="text-sm border border-gray-200 px-4 py-2 rounded-xl hover:bg-gray-50">
            <i class="fa-solid fa-calendar-check ml-1"></i>الحجوزات
        </a>
        <a href="{{ route('provider.services.create') }}" class="text-sm bg-emerald-600 text-white px-4 py-2 rounded-xl hover:bg-emerald-700">
            <i class="fa-solid fa-plus ml-1"></i>خدمة جديدة
        </a>
    </div>
</div>

@if($services->isEmpty())
    <div class="text-center py-20 text-gray-400">
        <i class="fa-solid fa-briefcase text-5xl mb-4"></i>
        <p class="mb-4">لا توجد خدمات بعد</p>
        <a href="{{ route('provider.services.create') }}" class="text-emerald-600 hover:underline">أضف خدمتك الأولى</a>
    </div>
@else
    <div class="grid gap-4">
        @foreach($services as $service)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex flex-col sm:flex-row sm:items-center gap-4 sm:gap-5">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-1">
                    <h3 class="font-semibold text-gray-900">{{ $service->name }}</h3>
                    @if(!$service->is_active)
                        <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">معطل</span>
                    @endif
                </div>
                <p class="text-sm text-gray-500 line-clamp-1">{{ $service->description }}</p>
                <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-gray-400 mt-2">
                    <span><i class="fa-solid fa-clock ml-1"></i>{{ $service->duration_minutes }} دقيقة</span>
                    <span><i class="fa-solid fa-tag ml-1"></i>{{ number_format($service->price, 0) }} ₪</span>
                    <span><i class="fa-solid fa-calendar ml-1"></i>{{ $service->slots_count }} وقت</span>
                    <span><i class="fa-solid fa-star ml-1"></i>{{ $service->reviews_count }} تقييم</span>
                </div>
            </div>
            <div class="flex gap-2 flex-shrink-0">
                <a href="{{ route('provider.slots.index', $service) }}"
                   class="text-xs border border-emerald-200 text-emerald-600 px-3 py-2 rounded-lg hover:bg-emerald-50">
                    <i class="fa-solid fa-clock ml-1"></i>الأوقات
                </a>
                <a href="{{ route('provider.services.edit', $service) }}"
                   class="text-xs border border-gray-200 text-gray-600 px-3 py-2 rounded-lg hover:bg-gray-50">
                    <i class="fa-solid fa-pen ml-1"></i>تعديل
                </a>
                <form action="{{ route('provider.services.destroy', $service) }}" method="POST"
                      onsubmit="return confirm('حذف هذه الخدمة؟')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-xs border border-red-100 text-red-500 px-3 py-2 rounded-lg hover:bg-red-50">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-4">{{ $services->links() }}</div>
@endif
@endsection
