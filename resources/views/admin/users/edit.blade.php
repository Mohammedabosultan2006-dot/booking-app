@extends('layouts.admin')
@section('title', 'تعديل مستخدم')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.users') }}" class="text-gray-400 hover:text-gray-600">
            <i class="fa-solid fa-arrow-right"></i>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">تعديل: {{ $user->name }}</h1>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('admin.users.update', $user) }}" method="POST">
            @csrf @method('PUT')
            @include('admin.users._form', ['user' => $user])

            <div class="mt-6 flex gap-3">
                <button type="submit" class="flex-1 bg-violet-600 text-white py-3 rounded-xl font-semibold hover:bg-violet-700 transition-colors">
                    حفظ التعديلات
                </button>
                <a href="{{ route('admin.users') }}"
                   class="px-5 py-3 border border-gray-200 rounded-xl text-gray-600 hover:bg-gray-50 transition-colors text-sm flex items-center">
                    إلغاء
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
