<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SeatBuddy - Smart Library Management System</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <!-- Libraries -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <script src="https://unpkg.com/lucide@latest"></script>

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#fff1f1',
                            100: '#ffe1e1',
                            200: '#ffc7c7',
                            300: '#ffa0a0',
                            400: '#ff6969',
                            500: '#f53003',
                            600: '#d92000',
                            700: '#b61b00',
                            800: '#961a00',
                            900: '#7d1b00',
                        },
                        dark: '#0a0a0a',
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
        body { font-family: 'Inter', sans-serif; background-color: #FDFDFC; }
        h1, h2, h3, h4 { font-family: 'Outfit', sans-serif; }
        
        .hero-gradient {
            background: radial-gradient(circle at 50% 50%, rgba(245, 48, 3, 0.08) 0%, rgba(255, 255, 255, 0) 70%);
        }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        .cta-shadow {
            box-shadow: 0 10px 30px -10px rgba(245, 48, 3, 0.5);
        }

        .floating {
            animation: floating 3s ease-in-out infinite;
        }

        @keyframes floating {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
    </style>
</head>
<body class="text-secondary overflow-x-hidden">
    <!-- Navigation -->
    <nav class="fixed top-0 w-full z-50 glass-card">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl overflow-hidden shadow-lg">
                        <img src="{{ asset('images/logo.png') }}" alt="SeatBuddy Logo" class="w-full h-full object-cover">
                    </div>
                    <span class="text-2xl font-bold font-outfit tracking-tight text-gray-900">SeatBuddy</span>
                </div>
                <div class="flex items-center gap-6">
                    <a href="#features" class="hidden md:block text-sm font-medium text-gray-600 hover:text-primary-500 transition-colors">Features</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 hero-gradient">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="inline-flex items-center px-4 py-2 rounded-full bg-primary-50 border border-primary-100 text-primary-600 text-xs font-bold uppercase tracking-widest mb-8 animate__animated animate__fadeInDown">
                🚀 Modernizing Libraries Worldwide
            </div>
            <h1 class="text-5xl md:text-7xl font-extrabold text-gray-900 tracking-tight mb-8 animate__animated animate__fadeIn">
                Manage Your Library <br/>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-500 to-orange-500">Like a Pro.</span>
            </h1>
            <p class="max-w-2xl mx-auto text-lg md:text-xl text-gray-500 mb-12 animate__animated animate__fadeIn animate__delay-1s">
                The ultimate all-in-one solution for seat allocation, fee management, and student tracking. Simple, powerful, and built for modern librarians.
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-4 animate__animated animate__fadeInUp animate__delay-1s">
                <a href="#features" class="px-8 py-4 bg-gray-900 text-white font-semibold rounded-2xl hover:bg-black transform hover:-translate-y-1 transition-all duration-200 flex items-center justify-center gap-2">
                    <i data-lucide="info" class="w-5 h-5"></i>
                    Explore Features
                </a>
                <a href="#" class="px-8 py-4 bg-primary-500 text-white font-bold rounded-2xl cta-shadow hover:bg-primary-600 transform hover:-translate-y-1 transition-all duration-200 flex items-center justify-center gap-3">
                    <svg viewBox="0 0 512 512" class="w-6 h-6 fill-current" xmlns="http://www.w3.org/2000/svg"><path d="M325.3 234.3L104.6 13l280.8 161.2-60.1 60.1zM47 36.8l202.9 202.9L47 442.6V36.8zM249.9 245.2L424.3 419.6c20.4-11.7 34.3-33.6 34.3-58.7 0-14.7-4.7-28.3-12.6-39.4L249.9 245.2zM104.6 499l220.7-126.7-60.1-60.1L47 475.2c13.7 15.1 33.4 23.8 57.6 23.8z"/></svg>
                    <span>Download on Google Play</span>
                </a>
            </div>

            <!-- Dashboard Mockup -->
            <div class="mt-20 relative max-w-5xl mx-auto animate__animated animate__fadeInUp animate__delay-2s">
                <div class="rounded-3xl border border-gray-100 shadow-2xl overflow-hidden bg-white p-2">
                    <img src="https://images.unsplash.com/photo-1497215728101-856f4ea42174?auto=format&fit=crop&q=80&w=1200" alt="Dashboard" class="rounded-2xl w-full">
                    <div class="absolute inset-x-0 bottom-0 h-32 bg-gradient-to-t from-white to-transparent"></div>
                </div>
                <!-- Floating Elements -->
                <div class="absolute -top-10 -right-10 w-48 glass-card rounded-2xl p-4 shadow-xl floating hidden lg:block">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="p-2 bg-green-100 rounded-lg text-green-600"><i data-lucide="check-circle" class="w-4 h-4"></i></div>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">Attendance</span>
                    </div>
                    <div class="text-xl font-bold text-gray-900">98% Efficient</div>
                </div>
                <div class="absolute top-1/2 -left-10 w-48 glass-card rounded-2xl p-4 shadow-xl floating hidden lg:block" style="animation-delay: 1s">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="p-2 bg-blue-100 rounded-lg text-blue-600"><i data-lucide="wallet" class="w-4 h-4"></i></div>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">Collections</span>
                    </div>
                    <div class="text-xl font-bold text-gray-900">Auto Reminders</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-20">
                <h2 class="text-3xl md:text-5xl font-bold text-gray-900 mb-4">Everything you need</h2>
                <p class="text-gray-500 max-w-xl mx-auto">Focus on growing your library while SeatBuddy handles the boring administrative tasks automatically.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="p-8 rounded-3xl bg-gray-50 border border-gray-100 hover:border-primary-200 transition-all duration-300 group">
                    <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center text-primary-500 shadow-sm group-hover:bg-primary-500 group-hover:text-white transition-all duration-300 mb-6">
                        <i data-lucide="armchair" class="w-7 h-7"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Smart Seat Allocation</h3>
                    <p class="text-gray-500 leading-relaxed">Visual seat mapping and one-click allocation. Know exactly who is sitting where at any time.</p>
                </div>

                <!-- Feature 2 -->
                <div class="p-8 rounded-3xl bg-gray-50 border border-gray-100 hover:border-primary-200 transition-all duration-300 group">
                    <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center text-primary-500 shadow-sm group-hover:bg-primary-500 group-hover:text-white transition-all duration-300 mb-6">
                        <i data-lucide="bell" class="w-7 h-7"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Push Notifications</h3>
                    <p class="text-gray-500 leading-relaxed">Send real-time updates and fee reminders directly to students' phones via our mobile app.</p>
                </div>

                <!-- Feature 3 -->
                <div class="p-8 rounded-3xl bg-gray-50 border border-gray-100 hover:border-primary-200 transition-all duration-300 group">
                    <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center text-primary-500 shadow-sm group-hover:bg-primary-500 group-hover:text-white transition-all duration-300 mb-6">
                        <i data-lucide="credit-card" class="w-7 h-7"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Auto Fee Tracking</h3>
                    <p class="text-gray-500 leading-relaxed">Automatic billing cycles and payment history. Reduce manual ledger entries by up to 90%.</p>
                </div>

                <!-- Feature 4 -->
                <div class="p-8 rounded-3xl bg-gray-50 border border-gray-100 hover:border-primary-200 transition-all duration-300 group">
                    <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center text-primary-500 shadow-sm group-hover:bg-primary-500 group-hover:text-white transition-all duration-300 mb-6">
                        <i data-lucide="users" class="w-7 h-7"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Student App</h3>
                    <p class="text-gray-500 leading-relaxed">A dedicated mobile experience for students to check attendance, pay fees, and receive updates.</p>
                </div>

                <!-- Feature 5 -->
                <div class="p-8 rounded-3xl bg-gray-50 border border-gray-100 hover:border-primary-200 transition-all duration-300 group">
                    <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center text-primary-500 shadow-sm group-hover:bg-primary-500 group-hover:text-white transition-all duration-300 mb-6">
                        <i data-lucide="bar-chart-2" class="w-7 h-7"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Detailed Reports</h3>
                    <p class="text-gray-500 leading-relaxed">Comprehensive analytics on revenue, attendance, and library occupancy rates.</p>
                </div>

                <!-- Feature 6 -->
                <div class="p-8 rounded-3xl bg-gray-50 border border-gray-100 hover:border-primary-200 transition-all duration-300 group">
                    <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center text-primary-500 shadow-sm group-hover:bg-primary-500 group-hover:text-white transition-all duration-300 mb-6">
                        <i data-lucide="shield-check" class="w-7 h-7"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Secure & Reliable</h3>
                    <p class="text-gray-500 leading-relaxed">Enterprise-grade security for your library data. Regular backups and 99.9% uptime guaranteed.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Mobile App Promo -->
    <section class="py-24 bg-gray-900 text-white overflow-hidden relative">
        <div class="absolute top-0 right-0 w-1/2 h-full opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row items-center gap-16">
                <div class="flex-1 text-center lg:text-left">
                    <h2 class="text-4xl md:text-5xl font-bold mb-6">Take control on the go</h2>
                    <p class="text-gray-400 text-lg mb-10 leading-relaxed">Download our mobile app to manage your students, track attendance, and send instant notifications from anywhere in the world.</p>
                    <div class="flex flex-wrap justify-center lg:justify-start gap-4">
                        <a href="#" class="inline-flex items-center gap-3 px-8 py-4 bg-white text-gray-900 font-bold rounded-2xl hover:bg-gray-100 transition-all shadow-xl">
                            <svg viewBox="0 0 512 512" class="w-6 h-6 fill-[#f53003]" xmlns="http://www.w3.org/2000/svg"><path d="M325.3 234.3L104.6 13l280.8 161.2-60.1 60.1zM47 36.8l202.9 202.9L47 442.6V36.8zM249.9 245.2L424.3 419.6c20.4-11.7 34.3-33.6 34.3-58.7 0-14.7-4.7-28.3-12.6-39.4L249.9 245.2zM104.6 499l220.7-126.7-60.1-60.1L47 475.2c13.7 15.1 33.4 23.8 57.6 23.8z"/></svg>
                            Download on Play Store
                        </a>
                        <a href="mailto:support@seatbuddy.in" class="inline-flex items-center gap-2 px-8 py-4 border-2 border-gray-700 text-white font-bold rounded-2xl hover:border-white transition-all">
                            <i data-lucide="help-circle" class="w-5 h-5"></i>
                            Support Center
                        </a>
                    </div>
                </div>
                <div class="flex-1 relative">
                    <div class="relative w-72 md:w-80 mx-auto transform rotate-6 hover:rotate-0 transition-all duration-500">
                        <div class="absolute inset-0 bg-primary-500 blur-3xl opacity-20"></div>
                        <img src="https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?auto=format&fit=crop&q=80&w=400&h=800" alt="Mobile App" class="rounded-[3rem] border-[12px] border-gray-800 shadow-2xl overflow-hidden">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white pt-24 pb-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-16">
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-lg overflow-hidden shadow-sm">
                            <img src="{{ asset('images/logo.png') }}" alt="SeatBuddy" class="w-full h-full object-cover">
                        </div>
                        <span class="text-xl font-bold font-outfit tracking-tight text-gray-900">SeatBuddy</span>
                    </div>
                    <p class="text-gray-500 max-w-sm">The world's most intuitive library management system. Transforming libraries through smart technology.</p>
                </div>
                <div>
                    <h4 class="text-gray-900 font-bold mb-6 italic uppercase tracking-widest text-xs">Product</h4>
                    <ul class="space-y-4 text-sm font-medium text-gray-600">
                        <li><a href="#features" class="hover:text-primary-500 transition-colors">Features</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-gray-900 font-bold mb-6 italic uppercase tracking-widest text-xs">Support</h4>
                    <ul class="space-y-4 text-sm font-medium text-gray-600">
                        <li><a href="mailto:support@seatbuddy.in" class="hover:text-primary-500 transition-colors">Contact Support</a></li>
                        <li><a href="{{ route('privacy') }}" class="hover:text-primary-500 transition-colors">Privacy Policy</a></li>
                        <li><a href="{{ route('terms') }}" class="hover:text-primary-500 transition-colors">Terms & Conditions</a></li>
                    </ul>
                </div>
            </div>
            <div class="pt-8 border-t border-gray-100 flex flex-col md:flex-row justify-between items-center text-gray-400 text-xs font-medium gap-4">
                <p>&copy; {{ date('Y') }} SeatBuddy Technologies. All rights reserved.</p>
                <div class="flex gap-6">
                    <a href="#" class="hover:text-gray-900 transition-colors">Twitter</a>
                    <a href="#" class="hover:text-gray-900 transition-colors">LinkedIn</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Initialize Lucide Icons
        lucide.createIcons();
    </script>
</body>
</html>
