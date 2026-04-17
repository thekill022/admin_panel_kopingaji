<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'Admin') — {{ config('app.name', 'Kopi Ngaji') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
        
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            [x-cloak] { display: none !important; }
            
            :root {
                --sidebar-width: 260px;
                --primary: #c4811f; /* Coffee Gold */
                --primary-dark: #a86618;
                --success: #22c55e;
                --danger: #ef4444;
                --warning: #f59e0b;
                --info: #3b82f6;
            }
            
            .sidebar-link-active {
                background-color: rgba(196, 129, 31, 0.1);
                border-left: 4px solid var(--primary);
                color: var(--primary);
                font-weight: 700;
            }

            /* Component - Card */
            .card {
                background: white;
                border-radius: 1rem;
                border: 1px solid #f3f4f6;
                box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
                overflow: hidden;
            }
            .card-header {
                padding: 1.25rem 1.5rem;
                border-bottom: 1px solid #f3f4f6;
                background-color: #f9fafb;
                display: flex;
                align-items: center;
                justify-content: space-between;
            }
            .card-title {
                font-weight: 700;
                color: #111827;
                font-size: 0.95rem;
            }

            /* Component - Button */
            .btn {
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                font-weight: 700;
                font-size: 0.8rem;
                padding: 0.5rem 1rem;
                border-radius: 0.75rem;
                transition: all 0.2s;
                cursor: pointer;
                text-transform: uppercase;
                tracking: 0.025em;
            }
            .btn-xs { padding: 0.25rem 0.5rem; font-size: 0.7rem; border-radius: 0.5rem; }
            .btn-sm { padding: 0.4rem 0.8rem; }
            
            .btn-primary { background: var(--primary); color: white; }
            .btn-primary:hover { background: var(--primary-dark); }
            
            .btn-secondary { background: #f3f4f6; color: #4b5563; border: 1px solid #e5e7eb; }
            .btn-secondary:hover { background: #e5e7eb; color: #1f2937; }
            
            .btn-success { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
            .btn-success:hover { background: #16a34a; color: white; }
            
            .btn-danger { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
            .btn-danger:hover { background: #dc2626; color: white; }

            .btn-warning { background: #fffbeb; color: #d97706; border: 1px solid #fef3c7; }
            .btn-warning:hover { background: #d97706; color: white; }

            /* Table Styles */
            .table-wrap { overflow-x: auto; }
            table { width: 100%; border-collapse: collapse; font-size: 0.875rem; }
            th { text-align: left; padding: 1rem 1.5rem; background: #f9fafb; font-weight: 700; color: #4b5563; text-transform: uppercase; font-size: 0.7rem; tracking: 0.05em; border-bottom: 1px solid #f3f4f6; }
            td { padding: 1rem 1.5rem; border-bottom: 1px solid #f3f4f6; color: #1f2937; vertical-align: middle; }
            tr:last-child td { border-bottom: 0; }
            tr:hover td { background: #f9fafb; }

            /* Badges */
            .badge {
                display: inline-flex;
                align-items: center;
                padding: 0.25rem 0.75rem;
                border-radius: 9999px;
                font-size: 0.7rem;
                font-weight: 800;
                text-transform: uppercase;
                letter-spacing: 0.025em;
            }
            .badge-pending { background: #fffbeb; color: #d97706; }
            .badge-approved, .badge-completed, .badge-success { background: #f0fdf4; color: #16a34a; }
            .badge-rejected, .badge-cancelled, .badge-danger { background: #fef2f2; color: #dc2626; }
            .badge-paid { background: #eff6ff; color: #2563eb; }

            /* Tabs */
            .tabs { display: flex; gap: 0.5rem; }
            .tab-item {
                padding: 0.5rem 1rem;
                font-size: 0.8rem;
                font-weight: 700;
                color: #6b7280;
                border-radius: 0.75rem;
                transition: all 0.2s;
            }
            .tab-item:hover { background: #f3f4f6; color: #111827; }
            .tab-item.active { background: #f3f4f6; color: var(--primary); }
            .tab-count {
                margin-left: 0.4rem;
                padding: 0.1rem 0.4rem;
                border-radius: 0.4rem;
                background: #e5e7eb;
                font-size: 0.65rem;
                color: #4b5563;
            }
            /* Table Responsive Wrap */
            .table-wrap {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                width: 100%;
            }

            /* Forms */
            .search-bar {
                position: relative;
            }
            .search-bar input {
                width: 100%;
                padding: 0.5rem 1rem 0.5rem 2.5rem;
                border-radius: 0.75rem;
                border: 1px solid #e5e7eb;
                font-size: 0.875rem;
                transition: border-color 0.2s;
            }
            .search-bar input:focus { border-color: var(--primary); outline: none; ring: 2px solid rgba(196, 129, 31, 0.1); }
            .search-bar-icon {
                position: absolute;
                left: 1rem;
                top: 50%;
                transform: translateY(-50%);
                color: #9ca3af;
                font-size: 0.9rem;
            }

            /* Empty States */
            .empty-state {
                padding: 4rem 2rem;
                text-align: center;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
            }
            .empty-icon {
                font-size: 3.5rem;
                color: #e5e7eb;
                margin-bottom: 1.5rem;
                opacity: 0.5;
            }
            .empty-state p {
                color: #9ca3af;
                font-size: 0.875rem;
                font-weight: 500;
                text-transform: uppercase;
                letter-spacing: 0.05em;
            }

            /* Pagination */
            .pagination-wrap {
                padding: 1.25rem 1.5rem;
                background-color: #f9fafb;
                border-top: 1px solid #f3f4f6;
                display: flex;
                align-items: center;
                justify-content: space-between;
                flex-wrap: wrap;
                gap: 1rem;
            }
            .pagination-wrap span {
                font-size: 0.75rem;
                font-weight: 600;
                color: #6b7280;
            }
            .pagination {
                display: flex;
                align-items: center;
                gap: 0.25rem;
            }
            .page-link {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-width: 2rem;
                height: 2rem;
                padding: 0 0.5rem;
                border-radius: 0.5rem;
                background: white;
                border: 1px solid #e5e7eb;
                color: #4b5563;
                font-size: 0.75rem;
                font-weight: 700;
                transition: all 0.2s;
                text-decoration: none;
            }
            .page-link:hover:not(.disabled) {
                border-color: var(--primary);
                color: var(--primary);
                background: #fffbeb;
            }
            .page-link.active {
                background: var(--primary);
                border-color: var(--primary);
                color: white;
            }
            .page-link.disabled {
                opacity: 0.5;
                cursor: not-allowed;
                background: #f9fafb;
            }

            /* Animations */
            @keyframes fadeInDown {
                from { opacity: 0; transform: translateY(-10px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .animate-fade-in-down { animation: fadeInDown 0.3s ease-out; }
        </style>
    </head>
    <body class="font-sans antialiased bg-gray-50 text-gray-900 overflow-x-hidden">
        <div class="min-h-screen flex" 
             x-data="{ sidebarOpen: window.innerWidth >= 1024 }"
             @resize.window="sidebarOpen = window.innerWidth >= 1024">
            
            <!-- Sidebar -->
            <aside 
                class="fixed inset-y-0 left-0 bg-white border-r border-gray-200 transition-all duration-300 z-50 flex flex-col overflow-x-hidden"
                :class="sidebarOpen ? 'w-full sm:w-[var(--sidebar-width)] lg:w-[var(--sidebar-width)] shadow-xl translate-x-0' : 'w-0 lg:w-20 -translate-x-full lg:translate-x-0'"
            >
                <!-- Brand -->
                <div class="h-16 flex items-center border-bottom border-gray-100 shrink-0 justify-between" :class="sidebarOpen ? 'px-6' : 'px-0 lg:justify-center'">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 shrink-0 bg-gradient-to-br from-amber-500 to-amber-700 rounded-lg flex items-center justify-center text-white text-xl">
                            <i class="fas fa-coffee"></i>
                        </div>
                        <div class="overflow-hidden whitespace-nowrap" x-show="sidebarOpen" style="display: none;">
                            <h1 class="font-bold text-gray-900 leading-none text-base">Kopi Ngaji</h1>
                            <span class="text-[10px] text-amber-600 font-bold uppercase tracking-wider">Admin Panel</span>
                        </div>
                    </div>
                    <!-- Tumbel button tutup khusus mobile saat penuh layar -->
                    <button @click="sidebarOpen = false" class="lg:hidden p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors" x-show="sidebarOpen" style="display: none;">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 overflow-y-auto overflow-x-hidden py-4 px-3 space-y-1" :class="sidebarOpen ? 'px-4 py-6' : 'px-2 py-4'">
                    <div class="px-3 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider overflow-hidden" x-show="sidebarOpen">
                        Menu Utama
                    </div>
                    
                    <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="fas fa-chart-pie">
                        Dashboard
                    </x-sidebar-link>

                    <div class="pt-4 px-3 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider overflow-hidden" x-show="sidebarOpen">
                        Manajemen
                    </div>

                    <x-sidebar-link :href="route('products.index')" :active="request()->routeIs('products.*')" icon="fas fa-box" badge="{{ \App\Models\Product::where('status','PENDING')->count() }}">
                        Produk
                    </x-sidebar-link>

                    <x-sidebar-link :href="route('orders.index')" :active="request()->routeIs('orders.*')" icon="fas fa-shopping-cart">
                        Pesanan
                    </x-sidebar-link>

                    <x-sidebar-link :href="route('withdrawals.index')" :active="request()->routeIs('withdrawals.*')" icon="fas fa-hand-holding-dollar" badge="{{ \App\Models\Withdrawal::where('status','PENDING')->count() }}">
                        Penarikan
                    </x-sidebar-link>

                    <x-sidebar-link :href="route('umkms.index')" :active="request()->routeIs('umkms.*')" icon="fas fa-store">
                        UMKM
                    </x-sidebar-link>

                    <x-sidebar-link :href="route('users.index')" :active="request()->routeIs('users.*')" icon="fas fa-users">
                        Pengguna
                    </x-sidebar-link>

                    <x-sidebar-link :href="route('reports.index')" :active="request()->routeIs('reports.*')" icon="fas fa-flag" badge="{{ \App\Models\Report::where('status','PENDING')->count() }}">
                        Laporan
                    </x-sidebar-link>

                    <x-sidebar-link :href="route('refunds.index')" :active="request()->routeIs('refunds.*')" icon="fas fa-undo-alt" badge="{{ \App\Models\Refund::where('status','PENDING')->count() }}">
                        Refund
                    </x-sidebar-link>

                    @if(auth()->user()->isSuperAdmin())
                        <div class="pt-4 px-3 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider overflow-hidden" x-show="sidebarOpen">
                            SuperAdmin
                        </div>
                        <x-sidebar-link :href="route('admin-accounts.index')" :active="request()->routeIs('admin-accounts.*')" icon="fas fa-user-shield">
                            Kelola Admin
                        </x-sidebar-link>
                    @endif
                </nav>

                <!-- User Profile Bottom -->
                <div class="border-t border-gray-100 shrink-0 transition-all duration-300" :class="sidebarOpen ? 'p-4' : 'p-2 py-4'">
                    <div class="bg-gray-50 rounded-xl flex items-center transition-all duration-300" :class="sidebarOpen ? 'p-3 gap-3 justify-start' : 'p-2 flex-col gap-2 justify-center'">
                        <div class="w-10 h-10 shrink-0 bg-amber-100 text-amber-700 rounded-full flex items-center justify-center font-bold text-sm">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0" x-show="sidebarOpen" style="display: none;">
                            <p class="text-sm font-bold text-gray-900 truncate">{{ auth()->user()->name ?: 'Admin' }}</p>
                            <p class="text-[10px] text-gray-500 font-medium uppercase">{{ auth()->user()->role }}</p>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="shrink-0 flex items-center justify-center" :class="sidebarOpen ? '' : 'w-full'">
                            @csrf
                            <button type="submit" class="p-2 text-gray-400 hover:text-red-500 transition-colors flex items-center justify-center" title="Logout">
                                <i class="fas fa-power-off"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </aside>

            <!-- Main Content Area -->
            <div 
                class="flex-1 flex flex-col transition-all duration-300 min-h-screen"
                :class="sidebarOpen ? 'lg:ml-[var(--sidebar-width)]' : 'lg:ml-20'"
            >
                <!-- Header -->
                <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-4 lg:px-8 sticky top-0 z-40">
                    <div class="flex items-center gap-4">
                        <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-lg hover:bg-gray-100 text-gray-500 transition-colors">
                            <i class="fas fa-bars"></i>
                        </button>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">@yield('page-title', 'Dashboard')</h2>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="hidden sm:flex items-center gap-2 text-gray-500 text-[13px]">
                            <i class="far fa-calendar-alt"></i>
                            <span>{{ now()->format('d M Y, H:i') }} WIB</span>
                        </div>
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                    <div>{{ Auth::user()->name }}</div>
                                    <div class="ms-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                <x-dropdown-link :href="route('profile.edit')">
                                    {{ __('Profile') }}
                                </x-dropdown-link>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <x-dropdown-link :href="route('logout')"
                                            onclick="event.preventDefault(); this.closest('form').submit();">
                                        {{ __('Log Out') }}
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                </header>

                <!-- Content -->
                <main class="p-4 lg:p-8 flex-1">
                    <!-- Session Status -->
                    @if(session('success'))
                        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 flex items-center gap-3 animate-fade-in-down">
                            <span class="w-8 h-8 bg-green-100 text-green-700 rounded-full flex items-center justify-center shrink-0">
                                <i class="fas fa-check"></i>
                            </span>
                            <span class="text-green-800 text-sm font-medium">{{ session('success') }}</span>
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 flex items-center gap-3 animate-fade-in-down">
                            <span class="w-8 h-8 bg-red-100 text-red-700 rounded-full flex items-center justify-center shrink-0">
                                <i class="fas fa-times"></i>
                            </span>
                            <span class="text-red-800 text-sm font-medium">{{ session('error') }}</span>
                        </div>
                    @endif

                    @yield('content')
                </main>

                <!-- Footer -->
                <footer class="py-6 px-4 text-center border-t border-gray-100">
                    <p class="text-xs text-gray-400">
                        &copy; {{ date('Y') }} Kopi Ngaji Admin Panel. 
                    </p>
                </footer>
            </div>
        </div>
    </body>
</html>
