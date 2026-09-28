@extends('layouts.app')
@section('title', $service->name)

@section('content')
@php $accent = $service->accent; @endphp

<!-- Hero -->
<div class="relative h-56 sm:h-72 rounded-3xl overflow-hidden mb-6">
    @if($service->image_url)
        <img src="{{ $service->image_url }}" alt="{{ $service->name }}" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
    @else
        <div class="w-full h-full bg-gradient-to-br {{ $accent['from'] }} {{ $accent['to'] }} relative">
            <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle, white 1.5px, transparent 1.5px); background-size: 20px 20px;"></div>
            <i class="fa-solid {{ $accent['icon'] }} absolute -left-6 -bottom-6 text-[10rem] text-white/10"></i>
            <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-black/0 to-transparent"></div>
        </div>
    @endif

    <div class="absolute bottom-0 right-0 left-0 p-6 sm:p-8">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                @if($service->average_rating > 0)
                    <span class="inline-flex items-center gap-1 bg-white/90 backdrop-blur-sm text-xs font-semibold text-gray-800 px-2.5 py-1 rounded-full mb-2">
                        <i class="fa-solid fa-star text-amber-500"></i>{{ $service->average_rating }}
                        <span class="text-gray-400 font-normal">({{ $service->reviews->count() }})</span>
                    </span>
                @endif
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white leading-tight drop-shadow-sm">{{ $service->name }}</h1>
            </div>
            <span class="bg-white text-indigo-700 text-lg font-extrabold px-4 py-2 rounded-2xl shadow-lg flex-shrink-0">
                {{ number_format($service->price, 0) }} <span class="text-sm font-medium">₪</span>
            </span>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    <!-- Left: Service Info -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex flex-wrap gap-3 mb-4">
                <span class="bg-indigo-50 text-indigo-700 text-sm px-3 py-1 rounded-full">
                    <i class="fa-regular fa-clock ml-1"></i>{{ $service->duration_minutes }} دقيقة
                </span>
                <span class="bg-emerald-50 text-emerald-700 text-sm px-3 py-1 rounded-full">
                    <i class="fa-solid fa-calendar-check ml-1"></i>{{ $service->available_slots_count }} موعد متاح
                </span>
                @if($service->location)
                <span class="bg-gray-50 text-gray-600 text-sm px-3 py-1 rounded-full">
                    <i class="fa-solid fa-location-dot ml-1"></i>{{ $service->location }}
                </span>
                @endif
            </div>

            <p class="text-gray-600 leading-relaxed">{{ $service->description }}</p>

            <div class="mt-5 pt-5 border-t border-gray-100 flex items-center gap-3">
                <img src="{{ $service->provider->avatar_url }}" class="w-12 h-12 rounded-full object-cover ring-2 ring-indigo-50">
                <div class="min-w-0">
                    <p class="font-semibold text-gray-900 flex items-center gap-1.5">
                        {{ $service->provider->name }}
                        <i class="fa-solid fa-circle-check text-indigo-500 text-xs" title="مزود موثوق"></i>
                    </p>
                    <p class="text-xs text-gray-400 truncate">{{ $service->provider->bio ?? 'مزود خدمة' }}</p>
                </div>
            </div>
        </div>

        <!-- Reviews -->
        @if($service->reviews->count() > 0)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="font-semibold text-gray-900 mb-4">
                التقييمات
                <span class="text-amber-500 mr-2">
                    <i class="fa-solid fa-star"></i> {{ $service->average_rating }}
                </span>
                <span class="text-gray-400 text-sm">({{ $service->reviews->count() }})</span>
            </h2>
            <div class="space-y-4">
                @foreach($service->reviews->take(5) as $review)
                <div class="flex gap-3">
                    <img src="{{ $review->customer->avatar_url }}" class="w-8 h-8 rounded-full object-cover flex-shrink-0">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 mb-1 flex-wrap">
                            <span class="font-medium text-sm text-gray-800">{{ $review->customer->name }}</span>
                            <div class="flex text-amber-400 text-xs">
                                @for($i=1;$i<=5;$i++)
                                    <i class="fa-{{ $i <= $review->rating ? 'solid' : 'regular' }} fa-star"></i>
                                @endfor
                            </div>
                        </div>
                        @if($review->comment)
                            <p class="text-gray-500 text-sm">{{ $review->comment }}</p>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <!-- Right: Slots -->
    <div class="space-y-4 lg:sticky lg:top-6 self-start">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h2 class="font-semibold text-gray-900 mb-4">
                <i class="fa-solid fa-calendar-days text-indigo-500 ml-1"></i>المواعيد المتاحة
            </h2>

            @if($slots->isEmpty())
                <p class="text-gray-400 text-sm text-center py-6">لا توجد مواعيد متاحة حالياً</p>
            @else
                <div class="max-h-[28rem] overflow-y-auto pl-1 -mr-1 pr-1">
                @foreach($slots as $date => $daySlots)
                <div class="mb-5">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase mb-2">
                        {{ \Carbon\Carbon::parse($date)->locale('ar')->isoFormat('dddd، D MMMM') }}
                    </h3>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach($daySlots as $slot)
                        @auth
                            <a href="{{ route('bookings.create', $slot) }}"
                               class="text-center text-sm py-2 px-3 rounded-xl border border-indigo-200 text-indigo-700 hover:bg-indigo-600 hover:text-white hover:border-indigo-600 transition-colors font-medium">
                                {{ $slot->start_at->format('H:i') }}
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                               class="text-center text-sm py-2 px-3 rounded-xl border border-gray-200 text-gray-500 hover:bg-gray-50 transition-colors">
                                {{ $slot->start_at->format('H:i') }}
                            </a>
                        @endauth
                        @endforeach
                    </div>
                </div>
                @endforeach
                </div>
            @endif
        </div>

        @guest
        <div class="bg-gradient-to-l from-indigo-600 to-purple-600 rounded-2xl p-5 text-center">
            <i class="fa-solid fa-lock text-white/70 mb-2"></i>
            <p class="text-sm text-white mb-3">سجّل دخولك لحجز موعد</p>
            <a href="{{ route('login') }}" class="bg-white text-indigo-700 text-sm font-semibold px-4 py-2 rounded-xl hover:bg-indigo-50 inline-block transition-colors">
                تسجيل الدخول
            </a>
        </div>
        @endguest
    </div>

</div>
@endsection
