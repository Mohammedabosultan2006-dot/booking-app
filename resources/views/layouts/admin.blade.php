<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'لوحة الإدارة')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-thumb { background: #4c1d95; border-radius: 8px; }
        .admin-shell { background-image: radial-gradient(circle at 100% 0%, rgba(139,92,246,0.08), transparent 40%); }
    </style>
</head>
<body class="min-h-screen bg-slate-100 text-gray-800" x-data="{ sidebarOpen: false }">

<div class="flex min-h-screen admin-shell">

    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : 'translate-x-full lg:translate-x-0'"
           class="fixed lg:static inset-y-0 right-0 z-40 w-64 bg-slate-950 text-slate-400 flex flex-col transition-transform duration-200 ease-in-out">
        <div class="h-16 flex items-center gap-2 px-6 border-b border-slate-800 flex-shrink-0">
            <div class="w-9 h-9 rounded-xl bg-violet-500 flex items-center justify-center">
                <i class="fa-solid fa-shield-halved text-white"></i>
            </div>
            <div>
                <p class="text-white font-bold text-sm leading-tight">لوحة الإدارة</p>
                <p class="text-slate-500 text-xs">BookingApp</p>
            </div>
        </div>

        <nav class="flex-1 overflow-y-auto py-5 px-3 space-y-1">
            @php
                $navItems = [
                    ['route' => 'admin.dashboard', 'icon' => 'fa-gauge-high', 'label' => 'اللوحة الرئيسية'],
                    ['route' => 'admin.users', 'icon' => 'fa-users', 'label' => 'المستخدمون'],
                ];
            @endphp
            @foreach($navItems as $item)
                <a href="{{ route($item['route']) }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-colors
                       {{ request()->routeIs($item['route'].'*') ? 'bg-violet-500/15 text-violet-300' : 'hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid {{ $item['icon'] }} w-4 text-center"></i>
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        <div class="p-3 border-t border-slate-800 space-y-1 flex-shrink-0">
            <a href="{{ route('services.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-400 hover:bg-slate-800 hover:text-white transition-colors">
                <i class="fa-solid fa-store w-4 text-center"></i>تصفح الموقع
            </a>
            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-400 hover:bg-slate-800 hover:text-white transition-colors">
                <i class="fa-solid fa-user w-4 text-center"></i>الملف الشخصي
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium text-red-400 hover:bg-red-500/10 transition-colors">
                    <i class="fa-solid fa-arrow-right-from-bracket w-4 text-center"></i>تسجيل الخروج
                </button>
            </form>
        </div>
    </aside>

    <!-- Mobile overlay -->
    <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
         class="fixed inset-0 bg-black/40 z-30 lg:hidden"></div>

    <!-- Main -->
    <div class="flex-1 flex flex-col min-w-0">
        <!-- Topbar -->
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-4 lg:px-8 flex-shrink-0">
            <div class="flex items-center gap-3">
                <button @click="sidebarOpen = true" class="lg:hidden text-gray-500">
                    <i class="fa-solid fa-bars text-lg"></i>
                </button>
                <h1 class="font-bold text-gray-900 text-lg">@yield('title', 'لوحة الإدارة')</h1>
            </div>
            <div class="flex items-center gap-3">
                <span class="hidden sm:inline-flex items-center gap-1.5 text-xs font-medium bg-violet-50 text-violet-700 px-3 py-1.5 rounded-full">
                    <i class="fa-solid fa-circle text-[6px]"></i>مدير النظام
                </span>
                <div class="flex items-center gap-2">
                    <img src="{{ auth()->user()->avatar_url }}" class="w-9 h-9 rounded-full object-cover border-2 border-violet-100">
                    <span class="hidden md:inline text-sm font-medium text-gray-700">{{ auth()->user()->name }}</span>
                </div>
            </div>
        </header>

        <!-- Flash Messages -->
        <div class="px-4 lg:px-8 pt-4">
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3 mb-4 flex items-center gap-2 text-sm">
                    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3 mb-4 flex items-center gap-2 text-sm">
                    <i class="fa-solid fa-circle-xmark"></i> {{ session('error') }}
                </div>
            @endif
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3 mb-4 text-sm">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <main class="flex-1 px-4 lg:px-8 py-6">
            @yield('content')
        </main>
    </div>
</div>

</body>
</html>
