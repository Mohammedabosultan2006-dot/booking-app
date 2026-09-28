@extends('layouts.app')
@section('title', 'إتمام الدفع')

@section('content')
<div class="max-w-xl mx-auto" x-data="{ selected: {{ $paymentMethods->first()->id ?? 'null' }} }">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('bookings.show', $booking) }}" class="text-gray-400 hover:text-gray-600">
            <i class="fa-solid fa-arrow-right"></i>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">إتمام الدفع</h1>
    </div>

    <!-- Amount Summary -->
    <div class="bg-gradient-to-l from-indigo-600 to-indigo-500 rounded-2xl p-6 mb-5 text-white">
        <p class="text-indigo-100 text-sm mb-1">{{ $service->name }}</p>
        <p class="text-3xl font-bold">{{ number_format($service->price, 0) }} <span class="text-lg font-normal">₪</span></p>
        <p class="text-indigo-100 text-xs mt-2">
            <i class="fa-solid fa-user ml-1"></i>{{ $service->provider->name }}
        </p>
    </div>

    @if($paymentMethods->isEmpty())
        <div class="bg-amber-50 border border-amber-200 text-amber-800 rounded-2xl p-5 text-sm">
            <i class="fa-solid fa-triangle-exclamation ml-1"></i>
            مزود الخدمة لم يضف بعد وسيلة استلام دفع. يرجى التواصل معه مباشرة لإتمام الحجز.
        </div>
    @else
        <form action="{{ route('bookings.payment.store', $booking) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <!-- Payment Method Cards -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">اختر وسيلة التحويل</label>
                <div class="space-y-3">
                    @foreach($paymentMethods as $method)
                    <label class="block cursor-pointer">
                        <input type="radio" name="payment_method_id" value="{{ $method->id }}"
                               x-model="selected" class="sr-only" {{ $loop->first ? 'checked' : '' }} required>
                        <div :class="selected == {{ $method->id }} ? 'border-indigo-500 ring-2 ring-indigo-100 bg-indigo-50/50' : 'border-gray-200'"
                             class="border rounded-2xl p-4 transition-colors">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-semibold text-gray-900 flex items-center gap-2">
                                    <i class="fa-solid {{ $method->type_icon }} text-indigo-500"></i>
                                    {{ $method->label }}
                                </span>
                                <span x-show="selected == {{ $method->id }}" class="text-indigo-500">
                                    <i class="fa-solid fa-circle-check"></i>
                                </span>
                            </div>
                            <div class="text-sm text-gray-600 space-y-1">
                                <div class="flex justify-between">
                                    <span class="text-gray-400">الاسم</span>
                                    <span>{{ $method->account_name }}</span>
                                </div>
                                <div class="flex flex-wrap justify-between items-center gap-x-2 gap-y-1">
                                    <span class="text-gray-400 flex-shrink-0">{{ $method->type === 'bank' ? 'رقم الحساب / آيبان' : 'رقم الجوال' }}</span>
                                    <span class="flex items-center gap-2 font-mono break-all" dir="ltr">
                                        {{ $method->account_number }}
                                        <button type="button"
                                            x-data="{ copied: false }"
                                            @click.prevent="navigator.clipboard.writeText('{{ $method->account_number }}'); copied = true; setTimeout(() => copied = false, 1500)"
                                            class="text-gray-400 hover:text-indigo-500">
                                            <i class="fa-regular" :class="copied ? 'fa-circle-check text-green-500' : 'fa-copy'"></i>
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            <!-- Proof Upload -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 space-y-4">
                <h3 class="font-semibold text-gray-900">
                    <i class="fa-solid fa-receipt text-indigo-500 ml-1"></i>بعد التحويل، ارفع إثبات الدفع
                </h3>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الرقم المرجعي / رقم العملية *</label>
                    <input type="text" name="reference_number" required value="{{ old('reference_number') }}"
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"
                        placeholder="مثال: 123456789">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">صورة إثبات التحويل *</label>
                    <input type="file" name="proof" accept="image/*" required
                        class="w-full text-sm text-gray-500 file:ml-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                </div>
            </div>

            <button type="submit" class="w-full bg-indigo-600 text-white py-3 rounded-xl font-semibold hover:bg-indigo-700 transition-colors">
                <i class="fa-solid fa-paper-plane ml-1"></i>إرسال إثبات الدفع
            </button>
        </form>
    @endif
</div>
@endsection
