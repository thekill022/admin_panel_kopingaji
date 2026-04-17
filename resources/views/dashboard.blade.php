@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-8">
    
    <!-- Welcome Header -->
    <div class="relative overflow-hidden bg-white rounded-2xl border border-gray-100 p-8 shadow-sm">
        <div class="relative z-10">
            <h3 class="text-2xl font-bold text-gray-900">Selamat Datang, {{ auth()->user()->name }}! 👋</h3>
            <p class="mt-1 text-gray-500">Berikut adalah ringkasan aktivitas platform Kopi Ngaji hari ini.</p>
        </div>
        <div class="absolute -right-8 -bottom-8 opacity-5 text-9xl">
            <i class="fas fa-coffee"></i>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <!-- Total Users -->
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow group">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-colors">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Users</div>
            </div>
            <div class="flex flex-col">
                <span class="text-3xl font-extrabold text-gray-900">{{ number_format($stats['total_users']) }}</span>
                <span class="text-sm text-gray-500 mt-1">{{ $stats['total_owners'] }} Owner · {{ $stats['total_buyers'] }} Buyer</span>
            </div>
        </div>

        <!-- Total UMKM -->
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow group">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center group-hover:bg-purple-600 group-hover:text-white transition-colors">
                    <i class="fas fa-store text-xl"></i>
                </div>
                <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">UMKM</div>
            </div>
            <div class="flex flex-col">
                <span class="text-3xl font-extrabold text-gray-900">{{ number_format($stats['total_umkms']) }}</span>
                <span class="text-sm text-gray-500 mt-1">Kedai & warung terdaftar</span>
            </div>
        </div>

        <!-- Total Products -->
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow group">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center group-hover:bg-amber-600 group-hover:text-white transition-colors">
                    <i class="fas fa-box text-xl"></i>
                </div>
                <div class="bg-amber-100 text-amber-700 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider">
                    {{ $stats['pending_products'] }} Pending
                </div>
            </div>
            <div class="flex flex-col">
                <span class="text-3xl font-extrabold text-gray-900">{{ number_format($stats['total_products']) }}</span>
                <span class="text-sm text-gray-500 mt-1 font-medium">Total Produk</span>
            </div>
        </div>

        <!-- Total Orders -->
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow group">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-50 text-green-600 rounded-xl flex items-center justify-center group-hover:bg-green-600 group-hover:text-white transition-colors">
                    <i class="fas fa-shopping-cart text-xl"></i>
                </div>
                <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Orders</div>
            </div>
            <div class="flex flex-col">
                <span class="text-3xl font-extrabold text-gray-900">{{ number_format($stats['total_orders']) }}</span>
                <span class="text-sm text-gray-500 mt-1">{{ $stats['completed_orders'] }} Selesai</span>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow group lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                    <i class="fas fa-wallet text-xl"></i>
                </div>
                <div class="text-xs font-bold text-emerald-600 uppercase tracking-wider bg-emerald-50 px-3 py-1 rounded-full">Revenue</div>
            </div>
            <div class="flex items-end justify-between">
                <div class="flex flex-col">
                    <span class="text-sm text-gray-400 font-semibold uppercase tracking-wide">Total Pendapatan</span>
                    <span class="text-4xl font-black text-gray-900 tracking-tight">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</span>
                </div>
                <div class="text-xs text-gray-400 italic mb-1">Dari pesanan yang selesai</div>
            </div>
        </div>

        <!-- Pending WD -->
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow group">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-orange-50 text-orange-600 rounded-xl flex items-center justify-center group-hover:bg-orange-600 group-hover:text-white transition-colors">
                    <i class="fas fa-hand-holding-dollar text-xl"></i>
                </div>
                <i class="fas fa-clock text-orange-200 text-xl"></i>
            </div>
            <div class="flex flex-col">
                <span class="text-3xl font-extrabold text-orange-600">{{ number_format($stats['pending_withdrawals']) }}</span>
                <span class="text-sm text-gray-500 mt-1">Penarikan Menunggu</span>
            </div>
        </div>

        <!-- Unverified Owners -->
        @if($stats['unverified_users'] > 0)
        <div class="bg-red-50 p-6 rounded-2xl border border-red-100 shadow-sm hover:shadow-md transition-shadow group">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-red-100 text-red-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-user-clock text-xl"></i>
                </div>
                <span class="flex h-3 w-3 relative">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                </span>
            </div>
            <div class="flex flex-col">
                <span class="text-3xl font-extrabold text-red-700">{{ $stats['unverified_users'] }}</span>
                <span class="text-sm text-red-600 mt-1 font-bold">Owner Perlu Verifikasi</span>
            </div>
        </div>
        @endif
    </div>

    <!-- Tables Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        
        <!-- Pending Products -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col">
            <div class="p-6 border-b border-gray-50 flex items-center justify-between bg-gray-50/50">
                <div class="flex items-center gap-2">
                    <i class="fas fa-box text-amber-500"></i>
                    <h4 class="font-bold text-gray-900">Produk Pending</h4>
                </div>
                <a href="{{ route('products.index', ['status' => 'pending']) }}" class="text-xs font-bold text-amber-600 hover:text-amber-700 transition-colors uppercase tracking-wider">Lihat Semua</a>
            </div>
            <div class="flex-1">
                @forelse($pending_products as $product)
                    <div class="p-4 border-b border-gray-50 last:border-0 hover:bg-gray-50 transition-colors flex items-center gap-4">
                        <div class="w-12 h-12 shrink-0 rounded-lg overflow-hidden bg-gray-100 border border-gray-200 flex items-center justify-center">
                            @if($product->image_url)
                                <img src="{{ $product->image_url }}" alt="" class="w-full h-full object-cover" />
                            @else
                                <i class="fas fa-box text-gray-300"></i>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-gray-900 truncate">{{ $product->name }}</p>
                            <p class="text-[11px] text-gray-500 truncate">{{ $product->umkm->name }}</p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <form method="POST" action="{{ route('products.approve', $product) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="w-7 h-7 bg-green-50 text-green-600 rounded-full flex items-center justify-center hover:bg-green-600 hover:text-white transition-all text-[10px]" title="Approve">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
                            <form method="POST" action="{{ route('products.reject', $product) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="w-7 h-7 bg-red-50 text-red-600 rounded-full flex items-center justify-center hover:bg-red-600 hover:text-white transition-all text-[10px]" title="Reject">
                                    <i class="fas fa-times"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center text-gray-400">
                        <i class="fas fa-check-circle text-4xl mb-3 opacity-20"></i>
                        <p class="text-xs font-medium uppercase tracking-widest">Semua produk sudah terverifikasi</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col">
            <div class="p-6 border-b border-gray-50 flex items-center justify-between bg-gray-50/50">
                <div class="flex items-center gap-2">
                    <i class="fas fa-shopping-cart text-blue-500"></i>
                    <h4 class="font-bold text-gray-900">Pesanan Terbaru</h4>
                </div>
                <a href="{{ route('orders.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-700 transition-colors uppercase tracking-wider">Lihat Semua</a>
            </div>
            <div class="flex-1">
                @forelse($recent_orders as $order)
                    <div class="p-4 border-b border-gray-50 last:border-0 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-sm font-bold text-gray-900">Order #{{ $order->id }}</span>
                            @php
                                $statusColors = [
                                    'PENDING' => 'bg-amber-100 text-amber-700',
                                    'PAID' => 'bg-blue-100 text-blue-700',
                                    'COMPLETED' => 'bg-green-100 text-green-700',
                                    'CANCELLED' => 'bg-red-100 text-red-700'
                                ];
                                $color = $statusColors[$order->status] ?? 'bg-gray-100 text-gray-700';
                            @endphp
                            <span class="text-[9px] font-black uppercase px-2 py-0.5 rounded {{ $color }} tracking-tighter">{{ $order->status }}</span>
                        </div>
                        <p class="text-[11px] text-gray-500 truncate">{{ $order->buyer->name }} · {{ $order->umkm->name }}</p>
                        <p class="text-xs font-bold text-amber-600 mt-1">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                    </div>
                @empty
                    <div class="py-12 text-center text-gray-400">
                        <i class="fas fa-inbox text-4xl mb-3 opacity-20"></i>
                        <p class="text-xs font-medium uppercase tracking-widest">Belum ada pesanan masuk</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Pending Withdrawals -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col">
            <div class="p-6 border-b border-gray-50 flex items-center justify-between bg-gray-50/50">
                <div class="flex items-center gap-2">
                    <i class="fas fa-hand-holding-dollar text-emerald-500"></i>
                    <h4 class="font-bold text-gray-900">Penarikan Pending</h4>
                </div>
                <a href="{{ route('withdrawals.index', ['status' => 'pending']) }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 transition-colors uppercase tracking-wider">Lihat Semua</a>
            </div>
            <div class="flex-1">
                @forelse($pending_withdrawals as $wd)
                    <div class="p-4 border-b border-gray-50 last:border-0 hover:bg-gray-50 transition-colors flex items-center justify-between">
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-gray-900 truncate">{{ $wd->owner->name }}</p>
                            <p class="text-[10px] text-gray-500 truncate">{{ $wd->bank_name }} · {{ $wd->bank_account }}</p>
                            <p class="text-xs font-black text-emerald-600 mt-1">Rp {{ number_format($wd->amount, 0, ',', '.') }}</p>
                        </div>
                        <div class="flex gap-2">
                            <form method="POST" action="{{ route('withdrawals.approve', $wd) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="w-8 h-8 bg-green-50 text-green-600 rounded-xl flex items-center justify-center hover:bg-green-600 hover:text-white transition-all text-xs" title="Approve">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
                            <form method="POST" action="{{ route('withdrawals.reject', $wd) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="w-8 h-8 bg-red-50 text-red-600 rounded-xl flex items-center justify-center hover:bg-red-600 hover:text-white transition-all text-xs" title="Reject">
                                    <i class="fas fa-times"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center text-gray-400">
                        <i class="fas fa-handshake text-4xl mb-3 opacity-20"></i>
                        <p class="text-xs font-medium uppercase tracking-widest">Tidak ada penarikan tertunda</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</div>
@endsection
