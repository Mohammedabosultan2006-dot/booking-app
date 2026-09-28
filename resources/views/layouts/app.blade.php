<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'نظام حجز المواعيد')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 text-gray-800 flex flex-col">

<!-- Navbar -->
<nav class="bg-white shadow-sm border-b border-gray-200" x-data="{ mobileOpen: false, userOpen: false }">
    <div class="max-w-7xl mx-auto px-4 flex items-center justify-between h-16">
        <a href="{{ route('services.index') }}" class="text-xl font-bold text-indigo-600 flex items-center gap-2 flex-shrink-0">
            <i class="fa-solid fa-calendar-check"></i> BookingApp
        </a>

        <!-- Desktop links -->
        <div class="hidden md:flex items-center gap-4">
            @guest
                <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-indigo-600">دخول</a>
                <a href="{{ route('register') }}" class="text-sm bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">تسجيل</a>
            @else
                @if(auth()->user()->isProvider() || auth()->user()->isAdmin())
                    <a href="{{ route('provider.dashboard') }}" class="text-sm text-gray-600 hover:text-indigo-600">
                        <i class="fa-solid fa-briefcase ml-1"></i>لوحتي
                    </a>
                @endif
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-600 hover:text-indigo-600">
                        <i class="fa-solid fa-gauge ml-1"></i>الإدارة
                    </a>
                @endif
                <a href="{{ route('bookings.index') }}" class="text-sm text-gray-600 hover:text-indigo-600">
                    <i class="fa-solid fa-ticket ml-1"></i>حجوزاتي
                </a>
                <div class="relative" @click.outside="userOpen = false">
                    <button @click="userOpen = !userOpen" class="flex items-center gap-2 text-sm text-gray-700">
                        <img src="{{ auth()->user()->avatar_url }}" class="w-8 h-8 rounded-full object-cover">
                        <span class="max-w-[8rem] truncate">{{ auth()->user()->name }}</span>
                        <i class="fa-solid fa-chevron-down text-xs"></i>
                    </button>
                    <div x-show="userOpen" x-cloak @click="userOpen = false"
                         class="absolute left-0 top-full mt-2 w-44 bg-white border border-gray-200 rounded-xl shadow-lg z-50">
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 rounded-t-xl">
                            <i class="fa-solid fa-user ml-1"></i>الملف الشخصي
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-right px-4 py-2 text-sm text-red-600 hover:bg-red-50 rounded-b-xl">
                                <i class="fa-solid fa-arrow-right-from-bracket ml-1"></i>خروج
                            </button>
                        </form>
                    </div>
                </div>
            @endguest
        </div>

        <!-- Mobile hamburger -->
        <button @click="mobileOpen = !mobileOpen" class="md:hidden text-gray-500 text-xl w-9 h-9 flex items-center justify-center flex-shrink-0">
            <i class="fa-solid" :class="mobileOpen ? 'fa-xmark' : 'fa-bars'"></i>
        </button>
    </div>

    <!-- Mobile menu -->
    <div x-show="mobileOpen" x-cloak class="md:hidden border-t border-gray-100 bg-white">
        <div class="px-4 py-3 space-y-1">
            @guest
                <a href="{{ route('login') }}" class="block px-3 py-2.5 rounded-lg text-sm text-gray-700 hover:bg-gray-50">دخول</a>
                <a href="{{ route('register') }}" class="block px-3 py-2.5 rounded-lg text-sm bg-indigo-600 text-white text-center hover:bg-indigo-700">تسجيل</a>
            @else
                <div class="flex items-center gap-3 px-3 py-2.5 border-b border-gray-100 mb-1">
                    <img src="{{ auth()->user()->avatar_url }}" class="w-9 h-9 rounded-full object-cover">
                    <span class="text-sm font-medium text-gray-800 truncate">{{ auth()->user()->name }}</span>
                </div>
                @if(auth()->user()->isProvider() || auth()->user()->isAdmin())
                    <a href="{{ route('provider.dashboard') }}" class="block px-3 py-2.5 rounded-lg text-sm text-gray-700 hover:bg-gray-50">
                        <i class="fa-solid fa-briefcase ml-2 w-4 text-center"></i>لوحتي
                    </a>
                @endif
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2.5 rounded-lg text-sm text-gray-700 hover:bg-gray-50">
                        <i class="fa-solid fa-gauge ml-2 w-4 text-center"></i>الإدارة
                    </a>
                @endif
                <a href="{{ route('bookings.index') }}" class="block px-3 py-2.5 rounded-lg text-sm text-gray-700 hover:bg-gray-50">
                    <i class="fa-solid fa-ticket ml-2 w-4 text-center"></i>حجوزاتي
                </a>
                <a href="{{ route('profile.edit') }}" class="block px-3 py-2.5 rounded-lg text-sm text-gray-700 hover:bg-gray-50">
                    <i class="fa-solid fa-user ml-2 w-4 text-center"></i>الملف الشخصي
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-right px-3 py-2.5 rounded-lg text-sm text-red-600 hover:bg-red-50">
                        <i class="fa-solid fa-arrow-right-from-bracket ml-2 w-4 text-center"></i>خروج
                    </button>
                </form>
            @endguest
        </div>
    </div>
</nav>

<!-- Flash Messages -->
<div class="max-w-7xl mx-auto px-4 pt-4">
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 mb-4 flex items-center gap-2">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3 mb-4 flex items-center gap-2">
            <i class="fa-solid fa-circle-xmark"></i> {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3 mb-4">
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>

<!-- Content -->
<main class="flex-1 w-full max-w-7xl mx-auto px-4 py-6">
    @isset($header)
        <div class="mb-6">
            {{ $header }}
        </div>
    @endisset

    @hasSection('content')
        @yield('content')
    @else
        {{ $slot ?? '' }}
    @endif
</main>

<footer class="text-center text-gray-400 text-sm py-8 mt-12 border-t border-gray-100">
    © {{ date('Y') }} BookingApp — جميع الحقوق محفوظة
</footer>

</body>
</html>
