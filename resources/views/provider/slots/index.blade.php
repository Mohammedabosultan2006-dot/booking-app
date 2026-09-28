@extends('layouts.provider')
@section('title', 'أوقات الخدمة')

@section('content')
<div class="mb-6 flex items-center gap-3">
    <a href="{{ route('provider.services.index') }}" class="text-gray-400 hover:text-gray-600">
        <i class="fa-solid fa-arrow-right"></i>
    </a>
    <div>
        <h1 class="text-2xl font-bold text-gray-900">أوقات: {{ $service->name }}</h1>
        <p class="text-sm text-gray-500">مدة كل موعد: {{ $service->duration_minutes }} دقيقة</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Add Slots Form -->
    <div class="space-y-4">
        <!-- Bulk Add -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h3 class="font-semibold text-gray-900 mb-4">
                <i class="fa-solid fa-wand-magic-sparkles text-emerald-500 ml-2"></i>إضافة يوم كامل تلقائياً
            </h3>
            <form action="{{ route('provider.slots.bulk', $service) }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">التاريخ</label>
                    <input type="date" name="date" required min="{{ date('Y-m-d') }}"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-300">
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">من</label>
                        <input type="time" name="start_time" value="09:00" required
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-300">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">إلى</label>
                        <input type="time" name="end_time" value="17:00" required
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-300">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">استراحة بين المواعيد (دقيقة)</label>
                    <input type="number" name="break" value="10" min="0" max="60"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-300">
                </div>
                <button type="submit" class="w-full bg-emerald-600 text-white py-2.5 rounded-xl text-sm font-semibold hover:bg-emerald-700">
                    <i class="fa-solid fa-bolt ml-1"></i>توليد الأوقات
                </button>
            </form>
        </div>

        <!-- Add Single Slot -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h3 class="font-semibold text-gray-900 mb-4">
                <i class="fa-solid fa-plus text-green-500 ml-2"></i>إضافة وقت محدد
            </h3>
            <form action="{{ route('provider.slots.store', $service) }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">بداية الموعد</label>
                    <input type="datetime-local" name="start_at" required
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-300">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">نهاية الموعد</label>
                    <input type="datetime-local" name="end_at" required
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-300">
                </div>
                <button type="submit" class="w-full bg-green-500 text-white py-2.5 rounded-xl text-sm font-semibold hover:bg-green-600">
                    إضافة الوقت
                </button>
            </form>
        </div>
    </div>

    <!-- Slots List -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-semibold text-gray-900">الأوقات القادمة</h3>
                <span class="text-sm text-gray-400">{{ $slots->total() }} وقت</span>
            </div>

            @if($slots->isEmpty())
                <div class="text-center py-12 text-gray-400">
                    <i class="fa-regular fa-clock text-4xl mb-3"></i>
                    <p>لا توجد أوقات مضافة بعد</p>
                </div>
            @else
                <div class="divide-y divide-gray-100">
                    @foreach($slots as $slot)
                    <div class="flex items-center justify-between px-5 py-3">
                        <div>
                            <p class="text-sm font-medium text-gray-800">
                                {{ $slot->start_at->locale('ar')->isoFormat('dddd، D MMM') }}
                            </p>
                            <p class="text-xs text-gray-400">
                                {{ $slot->start_at->format('H:i') }} - {{ $slot->end_at->format('H:i') }}
                            </p>
                        </div>
                        @if($slot->is_booked)
                            <span class="text-xs bg-red-50 text-red-600 px-2 py-1 rounded-full">محجوز</span>
                        @else
                            <div class="flex items-center gap-2">
                                <span class="text-xs bg-green-50 text-green-600 px-2 py-1 rounded-full">متاح</span>
                                <form action="{{ route('provider.slots.destroy', $slot) }}" method="POST"
                                      onsubmit="return confirm('حذف هذا الوقت؟')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:text-red-600 text-xs px-2 py-1">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                    @endforeach
                </div>
                <div class="p-4">{{ $slots->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
