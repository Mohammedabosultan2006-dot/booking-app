@extends('layouts.app')
@section('title', 'الخدمات المتاحة')

@section('content')
<!-- Hero -->
<div class="relative overflow-hidden rounded-3xl mb-10 bg-gradient-to-l from-indigo-700 via-indigo-600 to-purple-600 px-6 py-12 sm:px-12 sm:py-16">
    <div class="absolute -top-10 -left-10 w-56 h-56 bg-white/10 rounded-full blur-3xl"></div>
    <div class="absolute -bottom-16 -right-10 w-72 h-72 bg-purple-400/20 rounded-full blur-3xl"></div>

    <div class="relative text-center max-w-2xl mx-auto">
        <span class="inline-flex items-center gap-2 bg-white/15 text-white text-xs font-medium px-3 py-1.5 rounded-full mb-4 backdrop-blur-sm">
            <i class="fa-solid fa-sparkles text-amber-300"></i>منصّة حجز موثوقة
        </span>
        <h1 class="text-3xl sm:text-4xl font-extrabold text-white leading-tight mb-3">
            اكتشف أفضل الخدمات واحجز موعدك بثوانٍ
        </h1>
        <p class="text-indigo-100 text-sm sm:text-base mb-8">
            مزودو خدمة موثوقون، مواعيد متاحة فورياً، ودفع آمن وسهل
        </p>

        <form action="{{ route('services.index') }}" method="GET" class="relative max-w-lg mx-auto">
            <i class="fa-solid fa-magnifying-glass absolute top-1/2 -translate-y-1/2 right-5 text-gray-400"></i>
            <input type="text" name="q" value="{{ $search }}" placeholder="ابحث عن خدمة، صالون، عيادة..."
                class="w-full rounded-2xl border-0 pr-12 pl-4 py-4 text-sm shadow-lg focus:outline-none focus:ring-4 focus:ring-white/30">
            @if($search)
                <a href="{{ route('services.index') }}" class="absolute top-1/2 -translate-y-1/2 left-4 text-gray-400 hover:text-gray-600">
                    <i class="fa-solid fa-circle-xmark"></i>
                </a>
            @endif
        </form>

        <div class="flex items-center justify-center gap-8 mt-8 text-white">
            <div>
                <p class="text-2xl font-bold">{{ $services->total() }}</p>
                <p class="text-xs text-indigo-200">خدمة متاحة</p>
            </div>
            <div class="w-px h-8 bg-white/20"></div>
            <div>
                <p class="text-2xl font-bold">{{ $totalProviders }}</p>
                <p class="text-xs text-indigo-200">مزود خدمة</p>
            </div>
        </div>
    </div>
</div>

@if($search)
    <p class="text-sm text-gray-500 mb-5">
        نتائج البحث عن "<span class="font-semibold text-gray-800">{{ $search }}</span>" —
        <span class="font-medium">{{ $services->total() }}</span> خدمة
    </p>
@endif

@if($services->isEmpty())
    <div class="text-center py-20 text-gray-400">
        <i class="fa-solid fa-calendar-xmark text-5xl mb-4"></i>
        <p>{{ $search ? 'لا توجد نتائج مطابقة لبحثك' : 'لا توجد خدمات متاحة حالياً' }}</p>
        @if($search)
            <a href="{{ route('services.index') }}" class="text-indigo-600 hover:underline text-sm mt-2 inline-block">مسح البحث</a>
        @endif
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($services as $service)
        @php $accent = $service->accent; @endphp
        <a href="{{ route('services.show', $service) }}"
           class="group bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 block">

            <div class="relative h-48 overflow-hidden">
                @if($service->image_url)
                    <img src="{{ $service->image_url }}" alt="{{ $service->name }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-black/0 to-transparent"></div>
                @else
                    <div class="w-full h-full bg-gradient-to-br {{ $accent['from'] }} {{ $accent['to'] }} flex items-center justify-center relative">
                        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle, white 1px, transparent 1px); background-size: 16px 16px;"></div>
                        <i class="fa-solid {{ $accent['icon'] }} text-6xl text-white/90 group-hover:scale-110 transition-transform duration-500"></i>
                    </div>
                @endif

                <span class="absolute top-3 right-3 bg-white/95 backdrop-blur-sm text-indigo-700 text-sm font-bold px-3 py-1.5 rounded-full shadow-sm">
                    {{ number_format($service->price, 0) }} ₪
                </span>

                @if($service->average_rating > 0)
                    <span class="absolute top-3 left-3 bg-black/40 backdrop-blur-sm text-white text-xs font-medium px-2.5 py-1 rounded-full flex items-center gap-1">
                        <i class="fa-solid fa-star text-amber-400"></i>{{ $service->average_rating }}
                    </span>
                @else
                    <span class="absolute top-3 left-3 bg-black/40 backdrop-blur-sm text-white text-xs font-medium px-2.5 py-1 rounded-full">
                        جديد
                    </span>
                @endif
            </div>

            <div class="p-5">
                <h3 class="font-bold text-gray-900 text-lg leading-tight mb-1.5 group-hover:text-indigo-600 transition-colors">
                    {{ $service->name }}
                </h3>
                <p class="text-gray-500 text-sm mb-4 line-clamp-2 leading-relaxed">{{ $service->description }}</p>

                <div class="flex items-center gap-4 text-xs text-gray-400 mb-4">
                    <span><i class="fa-regular fa-clock ml-1"></i>{{ $service->duration_minutes }} دقيقة</span>
                    <span><i class="fa-solid fa-calendar-check ml-1"></i>{{ $service->available_slots_count }} موعد متاح</span>
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                    <div class="flex items-center gap-2 min-w-0">
                        <img src="{{ $service->provider->avatar_url }}" class="w-7 h-7 rounded-full object-cover flex-shrink-0">
                        <span class="text-xs font-medium text-gray-600 truncate">{{ $service->provider->name }}</span>
                    </div>
                    <span class="flex-shrink-0 text-xs font-semibold text-indigo-600 flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                        احجز الآن <i class="fa-solid fa-arrow-left text-[10px]"></i>
                    </span>
                </div>
            </div>
        </a>
        @endforeach
    </div>

    <div class="mt-8">{{ $services->links() }}</div>
@endif
@endsection
