<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Nyarugenge Mini Market</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    @stack('head')
</head>
<body class="h-full bg-gray-50 font-sans antialiased">

@php $user = auth()->user(); @endphp

<div class="flex h-full min-h-screen">

    {{-- ═══ SIDEBAR ══════════════════════════════════════════════ --}}
    <aside id="sidebar"
           class="sidebar w-64 min-h-screen flex flex-col shrink-0 fixed left-0 top-0 bottom-0 z-30
                  transition-transform duration-300 lg:translate-x-0 -translate-x-full">

        {{-- Logo --}}
        <div class="flex items-center gap-3 px-5 py-5 border-b border-white/10 shrink-0">
            <div class="w-9 h-9 rounded-xl bg-green-500 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-white font-bold text-sm leading-tight">Nyarugenge</p>
                <p class="text-green-400 text-xs font-medium">Mini Market</p>
            </div>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">

            {{-- All roles --}}
            <x-nav-section label="Overview" />
            <x-nav-link route="admin.dashboard" icon="grid" label="Dashboard" />

            {{-- Admin only --}}
            @if($user->isAdmin())
                <x-nav-section label="Management" />
                <x-nav-link route="admin.markets"    icon="map-pin"  label="Market Info" />
                <x-nav-link route="admin.vendors"    icon="users"    label="Vendors" />
                <x-nav-link route="admin.stalls"     icon="box"      label="Stalls" />
                <x-nav-link route="admin.devices"    icon="cpu"      label="IoT Devices" />
                <x-nav-link route="admin.thresholds" icon="sliders"  label="Thresholds" />
            @endif

        {{-- Admin + Inspector --}}
            @if($user->isAdmin() || $user->isInspector())
                <x-nav-section label="Monitoring" />
                <x-nav-link route="admin.sensor-readings" icon="activity"   label="Sensor Data" />
                <x-nav-link-badge route="admin.alerts"    icon="bell"       label="Alerts"
                    :count="\App\Models\Alert::where('status','open')->count()" />
                <x-nav-link route="admin.inspections"     icon="clipboard"
                    label="{{ auth()->user()->isInspector() ? 'My Inspections' : 'Inspections' }}" />
                <x-nav-link route="admin.reports"         icon="bar-chart-2" label="Reports" />
            @endif

            {{-- Admin only — system --}}
            @if($user->isAdmin())
                <x-nav-section label="System" />
                <x-nav-link route="admin.users" icon="user-check" label="User Management" />
            @endif

            {{-- Vendor portal --}}
            @if($user->isVendor())
                <x-nav-section label="My Space" />
                <x-nav-link route="admin.my-stall"       icon="box"       label="My Stalls & Sensors" />
                <x-nav-link route="admin.my-readings"    icon="activity"  label="Sensor Data" />
                <x-nav-link route="admin.my-inspections" icon="clipboard" label="Inspection Results" />
                <x-nav-link-badge route="admin.my-alerts" icon="bell" label="My Alerts"
                    :count="\App\Models\Alert::whereIn('stall_id', auth()->user()->vendor?->load('stalls')->stalls->pluck('id') ?? collect())->where('status','open')->count()" />
            @endif

        </nav>

        {{-- User footer --}}
        <div class="px-4 py-4 border-t border-white/10 shrink-0">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.profile') }}"
                   class="w-8 h-8 rounded-full bg-green-600 flex items-center justify-center text-white font-bold text-xs shrink-0 hover:bg-green-500 transition-colors">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </a>
                <div class="flex-1 min-w-0">
                    <a href="{{ route('admin.profile') }}"
                       class="text-white text-xs font-semibold truncate block hover:text-green-400 transition-colors">
                        {{ $user->name }}
                    </a>
                    <p class="text-gray-400 text-xs capitalize">
                        {{ str_replace('_', ' ', $user->role) }}
                    </p>
                </div>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="text-gray-400 hover:text-red-400 transition-colors" title="Logout">
                        <i data-feather="log-out" class="w-4 h-4"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Mobile overlay --}}
    <div id="sidebarOverlay" class="fixed inset-0 bg-black/50 z-20 hidden lg:hidden"
         onclick="closeSidebar()"></div>

    {{-- ═══ MAIN ════════════════════════════════════════════════ --}}
    <div class="flex-1 lg:ml-64 flex flex-col min-h-screen">

        {{-- Topbar --}}
        <header class="bg-white border-b border-gray-100 px-5 py-3.5 flex items-center justify-between sticky top-0 z-20 shadow-sm">
            <div class="flex items-center gap-3">
                <button onclick="toggleSidebar()" class="text-gray-400 hover:text-gray-600 lg:hidden p-1">
                    <i data-feather="menu" class="w-5 h-5"></i>
                </button>
                <div>
                    <h1 class="text-base font-bold text-gray-900 leading-tight">@yield('title', 'Dashboard')</h1>
                    <p class="text-xs text-gray-400 hidden sm:block">@yield('subtitle', 'Nyarugenge Mini Market — Hygiene Monitoring')</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="hidden md:flex items-center gap-1.5 text-gray-400 font-mono">
                    <i data-feather="clock" class="w-3.5 h-3.5"></i>
                    <span id="clock" class="text-xs"></span>
                </div>
                @if($user->isAdmin() || $user->isInspector())
                <a href="{{ route('admin.alerts') }}"
                   class="relative text-gray-400 hover:text-red-500 transition-colors p-1">
                    <i data-feather="bell" class="w-5 h-5"></i>
                    @php $openAlerts = \App\Models\Alert::where('status','open')->count(); @endphp
                    @if($openAlerts > 0)
                        <span class="absolute top-0 right-0 bg-red-500 rounded-full w-2 h-2"></span>
                    @endif
                </a>
                @endif
                <a href="{{ route('admin.profile') }}"
                   class="w-8 h-8 rounded-full bg-green-600 flex items-center justify-center text-white font-bold text-xs hover:bg-green-700 transition-colors"
                   title="Profile">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </a>
            </div>
        </header>

        {{-- Breadcrumb --}}
        @hasSection('breadcrumb')
        <div class="px-6 pt-4 text-xs text-gray-400 flex items-center gap-1.5">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-green-600">Dashboard</a>
            <i data-feather="chevron-right" class="w-3 h-3"></i>
            @yield('breadcrumb')
        </div>
        @endif

        {{-- Flash --}}
        @if(session('success'))
        <div class="mx-6 mt-4 p-3.5 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm flex items-center gap-2" id="flash-msg">
            <i data-feather="check-circle" class="w-4 h-4 text-green-600 shrink-0"></i>
            {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="mx-6 mt-4 p-3.5 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm flex items-center gap-2" id="flash-msg">
            <i data-feather="alert-circle" class="w-4 h-4 text-red-500 shrink-0"></i>
            {{ session('error') }}
        </div>
        @endif

        <main class="flex-1 p-5 md:p-6">
            @yield('content')
        </main>

        <footer class="px-6 py-3 border-t border-gray-100 text-xs text-gray-400 text-center">
            © {{ date('Y') }} Nyarugenge Mini Market — Hygiene Monitoring System &bull; Kigali, Rwanda
        </footer>
    </div>
</div>

<script>
    feather.replace({ width: 16, height: 16 });

    function tick() {
        const el = document.getElementById('clock');
        if (el) el.textContent = new Date().toLocaleTimeString('en-RW', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    }
    tick(); setInterval(tick, 1000);

    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('-translate-x-full');
        document.getElementById('sidebarOverlay').classList.toggle('hidden');
    }
    function closeSidebar() {
        document.getElementById('sidebar').classList.add('-translate-x-full');
        document.getElementById('sidebarOverlay').classList.add('hidden');
    }

    setTimeout(() => {
        const el = document.getElementById('flash-msg');
        if (el) { el.style.transition = 'opacity .5s'; el.style.opacity = '0'; setTimeout(() => el.remove(), 500); }
    }, 4000);
</script>
@stack('scripts')
</body>
</html>
