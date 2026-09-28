@extends('layouts.provider')
@section('title', isset($service) ? 'تعديل الخدمة' : 'خدمة جديدة')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('provider.services.index') }}" class="text-gray-400 hover:text-gray-600">
            <i class="fa-solid fa-arrow-right"></i>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">
            {{ isset($service) ? 'تعديل: ' . $service->name : 'خدمة جديدة' }}
        </h1>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <form action="{{ isset($service) ? route('provider.services.update', $service) : route('provider.services.store') }}"
              method="POST" enctype="multipart/form-data">
            @csrf
            @if(isset($service)) @method('PUT') @endif

            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">اسم الخدمة *</label>
                    <input type="text" name="name" value="{{ old('name', $service->name ?? '') }}" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-300"
                        placeholder="مثال: استشارة طبية، قصة شعر...">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الوصف</label>
                    <textarea name="description" rows="3"
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-300 resize-none"
                        placeholder="وصف مختصر للخدمة...">{{ old('description', $service->description ?? '') }}</textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">المدة (دقيقة) *</label>
                        <input type="number" name="duration_minutes" min="15" max="480"
                            value="{{ old('duration_minutes', $service->duration_minutes ?? 60) }}" required
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">السعر (شيكل) *</label>
                        <input type="number" name="price" min="0" step="0.01"
                            value="{{ old('price', $service->price ?? '') }}" required
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-300"
                            placeholder="0">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الموقع</label>
                    <input type="text" name="location" value="{{ old('location', $service->location ?? '') }}"
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-300"
                        placeholder="العنوان أو اسم المكان">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">صورة الخدمة</label>
                    <input type="file" name="image" accept="image/*"
                        class="w-full text-sm text-gray-500 file:ml-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                </div>

                @if(isset($service))
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="is_active" id="is_active" value="1"
                        {{ old('is_active', $service->is_active) ? 'checked' : '' }}
                        class="w-4 h-4 text-emerald-600 rounded">
                    <label for="is_active" class="text-sm text-gray-700">الخدمة مفعّلة</label>
                </div>
                @endif
            </div>

            <div class="mt-6 flex gap-3">
                <button type="submit" class="flex-1 bg-emerald-600 text-white py-3 rounded-xl font-semibold hover:bg-emerald-700 transition-colors">
                    {{ isset($service) ? 'حفظ التعديلات' : 'إضافة الخدمة' }}
                </button>
                <a href="{{ route('provider.services.index') }}"
                   class="px-5 py-3 border border-gray-200 rounded-xl text-gray-600 hover:bg-gray-50 transition-colors text-sm flex items-center">
                    إلغاء
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
