<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Denied — Nyarugenge Market</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <script src="https://unpkg.com/feather-icons"></script>
</head>
<body class="min-h-screen bg-gray-50 flex items-center justify-center font-sans">
    <div class="text-center max-w-md px-6">
        <div class="w-24 h-24 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-6">
            <i data-feather="shield-off" class="w-12 h-12 text-red-500"></i>
        </div>
        <p class="text-7xl font-extrabold text-red-200 mb-2 leading-none">403</p>
        <h1 class="text-2xl font-extrabold text-gray-900 mb-3">Access Denied</h1>
        <p class="text-gray-500 text-sm leading-relaxed mb-8">
            You don't have permission to access this area.
            If you believe this is a mistake, contact your system administrator.
        </p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            @auth
            <a href="{{ route('admin.dashboard') }}"
               class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl text-sm transition-colors">
                <i data-feather="home" class="w-4 h-4"></i>
                Go to Dashboard
            </a>
            @else
            <a href="{{ route('admin.login') }}"
               class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl text-sm transition-colors">
                <i data-feather="log-in" class="w-4 h-4"></i>
                Sign In
            </a>
            @endauth
            <a href="javascript:history.back()"
               class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-white hover:bg-gray-50 border border-gray-200 text-gray-700 font-semibold rounded-xl text-sm transition-colors">
                <i data-feather="arrow-left" class="w-4 h-4"></i>
                Go Back
            </a>
        </div>
    </div>
    <script>feather.replace();</script>
</body>
</html>
