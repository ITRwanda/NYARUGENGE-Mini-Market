<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — Nyarugenge Market</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/feather-icons"></script>
</head>
<body class="h-full min-h-screen font-sans antialiased flex">

    {{-- Left panel --}}
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden"
         style="background: linear-gradient(135deg, #0f1f0f 0%, #15803d 50%, #166534 100%);">

        <div class="absolute inset-0 opacity-10"
             style="background-image: radial-gradient(circle at 20% 50%, white 1px, transparent 1px),
                    radial-gradient(circle at 80% 20%, white 1px, transparent 1px);
                    background-size: 60px 60px;"></div>

        <div class="relative z-10 flex flex-col justify-between h-full p-12">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-green-400/20 border border-green-400/40 flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <span class="text-white font-bold text-lg">Nyarugenge Market</span>
            </div>

            <div>
                <h2 class="text-4xl font-extrabold text-white leading-tight mb-4">
                    Smart Hygiene<br>Monitoring System
                </h2>
                <p class="text-green-200 text-base leading-relaxed max-w-sm">
                    Real-time IoT sensor monitoring, automated alerts, and compliance
                    inspection management for Kigali's markets.
                </p>

                <div class="mt-10 grid grid-cols-2 gap-4">
                    <div class="bg-white/10 rounded-xl p-4 backdrop-blur-sm">
                        <div class="text-2xl font-bold text-white">3</div>
                        <div class="text-green-200 text-sm mt-1">Active Markets</div>
                    </div>
                    <div class="bg-white/10 rounded-xl p-4 backdrop-blur-sm">
                        <div class="text-2xl font-bold text-white">18+</div>
                        <div class="text-green-200 text-sm mt-1">IoT Sensors</div>
                    </div>
                    <div class="bg-white/10 rounded-xl p-4 backdrop-blur-sm">
                        <div class="text-2xl font-bold text-white">24/7</div>
                        <div class="text-green-200 text-sm mt-1">Monitoring</div>
                    </div>
                    <div class="bg-white/10 rounded-xl p-4 backdrop-blur-sm">
                        <div class="text-2xl font-bold text-white">12+</div>
                        <div class="text-green-200 text-sm mt-1">Active Vendors</div>
                    </div>
                </div>
            </div>

            <p class="text-green-400/60 text-xs">© {{ date('Y') }} Nyarugenge District, Kigali, Rwanda</p>
        </div>
    </div>

    {{-- Right panel --}}
    <div class="flex-1 flex items-center justify-center px-6 py-12 bg-gray-50">
        <div class="w-full max-w-md">

            {{-- Mobile logo --}}
            <div class="flex lg:hidden items-center gap-3 mb-8">
                <div class="w-9 h-9 rounded-xl bg-green-600 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <span class="font-bold text-gray-900">Nyarugenge Market</span>
            </div>

            <div class="mb-8">
                <h1 class="text-2xl font-extrabold text-gray-900">Welcome back</h1>
                <p class="text-gray-500 mt-1 text-sm">Sign in to the monitoring dashboard</p>
            </div>

            {{-- Errors --}}
            @if($errors->any())
                <div class="mb-5 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm flex items-start gap-2">
                    <i data-feather="alert-circle" class="w-4 h-4 shrink-0 mt-0.5"></i>
                    <div>{{ $errors->first() }}</div>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.post') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Email address</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400">
                            <i data-feather="mail" class="w-4 h-4"></i>
                        </span>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            placeholder="admin@market.rw"
                            class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500 bg-white transition @error('email') border-red-400 @enderror"
                        >
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">Password</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400">
                            <i data-feather="lock" class="w-4 h-4"></i>
                        </span>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            required
                            placeholder="••••••••"
                            class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500 bg-white transition"
                        >
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                        Remember me
                    </label>
                </div>

                <button type="submit"
                        class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-4 rounded-xl text-sm transition-colors duration-150 flex items-center justify-center gap-2">
                    <i data-feather="log-in" class="w-4 h-4"></i>
                    Sign In
                </button>
            </form>

            {{-- Demo credentials hint --}}
            <div class="mt-6 p-4 bg-blue-50 border border-blue-100 rounded-xl text-xs text-blue-700">
                <p class="font-semibold mb-1">Demo credentials:</p>
                <p>Admin: <span class="font-mono">admin@market.rw</span> / <span class="font-mono">password</span></p>
                <p>Inspector: <span class="font-mono">p.nzeyimana@market.rw</span> / <span class="font-mono">password</span></p>
            </div>
        </div>
    </div>

<script>feather.replace();</script>
</body>
</html>
