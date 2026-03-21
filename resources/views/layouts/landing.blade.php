<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - {{ config('app.name', 'SeatBuddy') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN for quick and beautiful styling -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#F53003',
                        secondary: '#1b1b18',
                    },
                    fontFamily: {
                        outfit: ['Outfit', 'sans-serif'],
                        inter: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, h4 { font-family: 'Outfit', sans-serif; }
        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .dark .glass {
            background: rgba(22, 22, 21, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC] min-h-screen">
    <nav class="sticky top-0 z-50 glass">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center">
                    <a href="/" class="text-2xl font-bold font-outfit text-primary flex items-center gap-2">
                        <div class="w-10 h-10 rounded-lg overflow-hidden border border-gray-100 dark:border-gray-800">
                            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-full h-full object-cover">
                        </div>
                        SeatBuddy
                    </a>
                </div>
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-4">
                        <a href="/" class="px-3 py-2 rounded-md text-sm font-medium hover:text-primary transition-colors">Home</a>
                        <a href="{{ route('login') }}" class="px-3 py-2 rounded-md text-sm font-medium hover:text-primary transition-colors">Login</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="glass rounded-3xl p-8 md:p-12 shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-primary/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 left-0 -ml-16 -mb-16 w-64 h-64 bg-primary/5 rounded-full blur-3xl"></div>
            
            <div class="relative">
                @yield('content')
            </div>
        </div>
    </main>

    <footer class="mt-auto py-12 border-t border-gray-100 dark:border-gray-800">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                &copy; {{ date('Y') }} SeatBuddy. All rights reserved.
            </p>
            <div class="mt-4 flex justify-center space-x-6">
                <a href="/privacy-policy" class="text-sm text-gray-500 hover:text-primary transition-colors">Privacy Policy</a>
                <a href="/terms-conditions" class="text-sm text-gray-500 hover:text-primary transition-colors">Terms & Conditions</a>
            </div>
        </div>
    </footer>
</body>
</html>
