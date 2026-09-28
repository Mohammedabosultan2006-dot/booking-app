@extends('layouts.admin')
@section('title', 'لوحة الإدارة')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-bold text-gray-900">لوحة الإدارة</h1>
    <p class="text-gray-500">نظرة عامة على النظام</p>
</div>

<!-- Stats -->
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
    @php
    $cards = [
        ['label'=>'المستخدمون', 'value'=>$stats['total_users'],    'icon'=>'fa-users',          'color'=>'text-blue-500',   'bg'=>'bg-blue-50'],
        ['label'=>'الخدمات',    'value'=>$stats['total_services'],  'icon'=>'fa-briefcase',      'color'=>'text-violet-500', 'bg'=>'bg-violet-50'],
        ['label'=>'الحجوزات',   'value'=>$stats['total_bookings'],  'icon'=>'fa-calendar-check', 'color'=>'text-green-500',  'bg'=>'bg-green-50'],
        ['label'=>'الإيرادات',  'value'=>number_format($stats['revenue'],0).' ₪', 'icon'=>'fa-coins', 'color'=>'text-amber-500', 'bg'=>'bg-amber-50'],
        ['label'=>'دفعات بالانتظار', 'value'=>$stats['payments_pending'], 'icon'=>'fa-hourglass-half', 'color'=>'text-rose-500', 'bg'=>'bg-rose-50'],
        ['label'=>'دفعات موثّقة',    'value'=>$stats['payments_verified'], 'icon'=>'fa-circle-check', 'color'=>'text-emerald-500', 'bg'=>'bg-emerald-50'],
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
        <div class="p-5 border-b border-gray-100">
            <h2 class="font-semibold text-gray-900">آخر الحجوزات</h2>
        </div>
        <div class="divide-y divide-gray-100">
            @foreach($recentBookings as $booking)
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
            @endforeach
        </div>
    </div>

    <!-- Stats Breakdown + Top Services -->
    <div class="lg:col-span-2 space-y-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h3 class="font-semibold text-gray-900 mb-4">حالة الحجوزات</h3>
            <div class="space-y-3">
                @foreach([['label'=>'انتظار','val'=>$stats['pending'],'color'=>'bg-yellow-400'],['label'=>'مؤكدة','val'=>$stats['confirmed'],'color'=>'bg-green-400'],['label'=>'ملغية','val'=>$stats['cancelled'],'color'=>'bg-red-400']] as $item)
                <div>
                    <div class="flex justify-between text-xs text-gray-500 mb-1">
                        <span>{{ $item['label'] }}</span>
                        <span>{{ $item['val'] }}</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        @php $pct = $stats['total_bookings'] > 0 ? ($item['val'] / $stats['total_bookings'] * 100) : 0; @endphp
                        <div class="{{ $item['color'] }} h-2 rounded-full" style="width: {{ $pct }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h3 class="font-semibold text-gray-900 mb-4">أكثر الخدمات حجزاً</h3>
            <div class="space-y-2">
                @foreach($topServices as $i => $svc)
                <div class="flex items-center gap-2 text-sm">
                    <span class="w-5 h-5 bg-violet-100 text-violet-600 rounded-full text-xs flex items-center justify-center font-bold flex-shrink-0">{{ $i+1 }}</span>
                    <span class="flex-1 text-gray-700 truncate">{{ $svc->name }}</span>
                    <span class="text-gray-400 text-xs">{{ $svc->bookings_count }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <a href="{{ route('admin.users') }}"
           class="block bg-violet-600 text-white text-center py-3 rounded-xl text-sm font-semibold hover:bg-violet-700 transition-colors">
            <i class="fa-solid fa-users ml-2"></i>إدارة المستخدمين
        </a>
    </div>
</div>
@endsection
