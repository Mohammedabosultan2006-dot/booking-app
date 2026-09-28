@extends('layouts.provider')
@section('title', 'وسائل الدفع')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">وسائل الدفع</h1>
        <p class="text-sm text-gray-500">أضف حسابات استلام الدفع الخاصة بك ليتمكن العملاء من التحويل إليها</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Add Form -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 h-fit">
        <h3 class="font-semibold text-gray-900 mb-4">
            <i class="fa-solid fa-plus text-emerald-500 ml-1"></i>إضافة وسيلة دفع
        </h3>
        <form action="{{ route('provider.payment-methods.store') }}" method="POST" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">النوع</label>
                <select name="type" required class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-300 bg-white">
                    <option value="bank">تحويل بنكي</option>
                    <option value="palpay">PalPay</option>
                    <option value="wallet">محفظة جوال أخرى</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">اسم الوسيلة (يظهر للعميل)</label>
                <input type="text" name="label" required placeholder="مثال: بنك فلسطين"
                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-300">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">اسم صاحب الحساب</label>
                <input type="text" name="account_name" required
                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-300">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">رقم الحساب / آيبان / رقم الجوال</label>
                <input type="text" name="account_number" required
                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-300">
            </div>
            <button type="submit" class="w-full bg-emerald-500 text-white py-2.5 rounded-xl text-sm font-semibold hover:bg-emerald-600">
                إضافة
            </button>
        </form>
    </div>

    <!-- List -->
    <div class="lg:col-span-2 space-y-4">
        @forelse($paymentMethods as $method)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
                <span class="font-semibold text-gray-900 flex items-center gap-2">
                    <i class="fa-solid {{ $method->type_icon }} text-emerald-500"></i>
                    {{ $method->label }}
                    @if(!$method->is_active)
                        <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">معطّلة</span>
                    @endif
                </span>
                <div class="flex gap-2 flex-shrink-0">
                    <form action="{{ route('provider.payment-methods.update', $method) }}" method="POST">
                        @csrf @method('PUT')
                        <input type="hidden" name="type" value="{{ $method->type }}">
                        <input type="hidden" name="label" value="{{ $method->label }}">
                        <input type="hidden" name="account_name" value="{{ $method->account_name }}">
                        <input type="hidden" name="account_number" value="{{ $method->account_number }}">
                        <input type="hidden" name="is_active" value="{{ $method->is_active ? '0' : '1' }}">
                        <button type="submit" class="text-xs border border-gray-200 text-gray-600 px-3 py-1.5 rounded-lg hover:bg-gray-50">
                            {{ $method->is_active ? 'تعطيل' : 'تفعيل' }}
                        </button>
                    </form>
                    <form action="{{ route('provider.payment-methods.destroy', $method) }}" method="POST"
                          onsubmit="return confirm('حذف وسيلة الدفع هذه؟')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs border border-red-100 text-red-500 px-3 py-1.5 rounded-lg hover:bg-red-50">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
            <div class="text-sm text-gray-600 space-y-1">
                <div class="flex flex-wrap justify-between gap-x-2"><span class="text-gray-400 flex-shrink-0">صاحب الحساب</span><span class="break-words text-left">{{ $method->account_name }}</span></div>
                <div class="flex flex-wrap justify-between gap-x-2"><span class="text-gray-400 flex-shrink-0">الرقم</span><span class="font-mono break-all" dir="ltr">{{ $method->account_number }}</span></div>
            </div>
        </div>
        @empty
        <div class="text-center py-16 text-gray-400 bg-white rounded-2xl border border-gray-100">
            <i class="fa-solid fa-wallet text-4xl mb-3"></i>
            <p>لم تضف أي وسيلة دفع بعد</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
