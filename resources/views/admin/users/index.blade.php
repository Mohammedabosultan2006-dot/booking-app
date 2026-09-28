@extends('layouts.admin')
@section('title', 'إدارة المستخدمين')

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-gray-600">
            <i class="fa-solid fa-arrow-right"></i>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">إدارة المستخدمين</h1>
    </div>
    <a href="{{ route('admin.users.create') }}"
       class="text-sm bg-violet-600 text-white px-4 py-2 rounded-xl hover:bg-violet-700">
        <i class="fa-solid fa-plus ml-1"></i>إضافة مستخدم
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
    <table class="w-full text-sm min-w-[820px]">
        <thead class="bg-gray-50 border-b border-gray-100">
            <tr>
                <th class="text-right px-5 py-3 text-gray-500 font-medium">المستخدم</th>
                <th class="text-right px-5 py-3 text-gray-500 font-medium">رقم الهوية</th>
                <th class="text-right px-5 py-3 text-gray-500 font-medium">الجوال</th>
                <th class="text-right px-5 py-3 text-gray-500 font-medium">الدور</th>
                <th class="text-right px-5 py-3 text-gray-500 font-medium">الحجوزات</th>
                <th class="text-right px-5 py-3 text-gray-500 font-medium">الخدمات</th>
                <th class="text-right px-5 py-3 text-gray-500 font-medium">إجراءات</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($users as $user)
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-4">
                    <div class="flex items-center gap-2">
                        <img src="{{ $user->avatar_url }}" class="w-8 h-8 rounded-full object-cover">
                        <div>
                            <p class="font-medium text-gray-900">{{ $user->name }}</p>
                            <p class="text-xs text-gray-400">{{ $user->email ?? 'لا يوجد إيميل' }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-5 py-4 text-gray-600 font-mono" dir="ltr">{{ $user->national_id }}</td>
                <td class="px-5 py-4 text-gray-600 font-mono" dir="ltr">{{ $user->phone }}</td>
                <td class="px-5 py-4">
                    <form action="{{ route('admin.users.role', $user) }}" method="POST">
                        @csrf @method('PATCH')
                        <select name="role" onchange="this.form.submit()"
                            class="text-xs border border-gray-200 rounded-lg px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-violet-300 bg-white">
                            <option value="customer"  {{ $user->role==='customer'  ? 'selected':'' }}>عميل</option>
                            <option value="provider"  {{ $user->role==='provider'  ? 'selected':'' }}>مزود</option>
                            <option value="admin"     {{ $user->role==='admin'     ? 'selected':'' }}>مدير</option>
                        </select>
                    </form>
                </td>
                <td class="px-5 py-4 text-gray-600">{{ $user->bookings_count }}</td>
                <td class="px-5 py-4 text-gray-600">{{ $user->services_count }}</td>
                <td class="px-5 py-4">
                    <div class="flex gap-2">
                        <a href="{{ route('admin.users.edit', $user) }}"
                           class="text-xs border border-gray-200 text-gray-600 px-3 py-1.5 rounded-lg hover:bg-gray-50">
                            <i class="fa-solid fa-pen"></i>
                        </a>
                        @if($user->id !== auth()->id())
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                              onsubmit="return confirm('حذف هذا المستخدم؟')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs text-red-500 hover:text-red-700 border border-red-100 px-3 py-1.5 rounded-lg hover:bg-red-50">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>
    <div class="p-4">{{ $users->links() }}</div>
</div>
@endsection
