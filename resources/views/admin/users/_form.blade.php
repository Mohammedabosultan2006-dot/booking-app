@php $isEdit = $user !== null; @endphp

<div class="space-y-5">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">الاسم *</label>
        <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" required
            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-violet-300">
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">رقم الهوية *</label>
            <input type="text" name="national_id" inputmode="numeric" pattern="\d{9}" maxlength="9"
                value="{{ old('national_id', $user->national_id ?? '') }}" required
                class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-violet-300" dir="ltr">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">رقم الجوال *</label>
            <input type="tel" name="phone" value="{{ old('phone', $user->phone ?? '') }}" required
                class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-violet-300" dir="ltr">
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">البريد الإلكتروني (اختياري)</label>
        <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}"
            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-violet-300" dir="ltr">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">الدور *</label>
        <select name="role" required class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-violet-300 bg-white">
            @php $role = old('role', $user->role ?? 'customer'); @endphp
            <option value="customer" {{ $role === 'customer' ? 'selected' : '' }}>عميل</option>
            <option value="provider" {{ $role === 'provider' ? 'selected' : '' }}>مزود خدمة</option>
            <option value="admin"    {{ $role === 'admin'    ? 'selected' : '' }}>مدير</option>
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">نبذة (اختياري)</label>
        <textarea name="bio" rows="2"
            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-violet-300 resize-none">{{ old('bio', $user->bio ?? '') }}</textarea>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-3 border-t border-gray-100">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">{{ $isEdit ? 'كلمة مرور جديدة (اختياري)' : 'كلمة المرور *' }}</label>
            <input type="password" name="password" {{ $isEdit ? '' : 'required' }} autocomplete="new-password"
                class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-violet-300">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">تأكيد كلمة المرور</label>
            <input type="password" name="password_confirmation" {{ $isEdit ? '' : 'required' }} autocomplete="new-password"
                class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-violet-300">
        </div>
    </div>
</div>
