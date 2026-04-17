@extends('layouts.app')

@section('title', $product->name)
@section('page-title', 'Detail Produk')
@section('page-subtitle', $product->name)

@section('topbar-actions')
    <a href="{{ route('products.index') }}" class="btn btn-secondary btn-sm">← Kembali</a>
@endsection

@section('content')

    <style>
        .product-detail-container {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .product-detail-container .card-body {
            padding: 20px;
        }

        .product-detail-container .card {
            margin-bottom: 20px;
        }

        @media (min-width: 768px) {
            .product-detail-container {
                grid-template-columns: 380px 1fr;
            }
        }

        .product-image {
            width: 100%;
            aspect-ratio: 1;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 16px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            font-size: 13px;
        }

        .action-body {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .placeholder-image {
            width: 100%;
            aspect-ratio: 1;
            background: var(--dark-700);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 64px;
            margin-bottom: 16px;
        }
    </style>

    <div class="product-detail-container">

        {{-- Left: Image & Status --}}
        <div>
            <div class="card mb-4">
                <div class="card-body">
                    @if ($product->image_url)
                        <img src="{{ asset(env('IMG_URL') . '/' . $product->image_url) }}" alt="{{ $product->name }}"
                            class="product-image" />
                    @else
                        <div class="placeholder-image">📦</div>
                    @endif

                    <h2 style="font-size:18px;font-weight:700;color:var(--text-primary);margin-bottom:8px;">
                        {{ $product->name }}</h2>
                    <p style="font-size:13px;color:var(--text-muted);margin-bottom:16px;">
                        {{ $product->description ?? 'Tidak ada deskripsi.' }}</p>

                    <div class="divider"></div>

                    <div class="info-grid">
                        <div>
                            <div style="color:var(--text-muted);margin-bottom:2px;">Harga Jual</div>
                            <div style="color:var(--coffee-400);font-weight:700;font-size:16px;">Rp
                                {{ number_format($product->price, 0, ',', '.') }}</div>
                        </div>
                        <div>
                            <div style="color:var(--text-muted);margin-bottom:2px;">Harga Modal</div>
                            <div style="color:var(--text-secondary);font-weight:600;">Rp
                                {{ number_format($product->cost_price, 0, ',', '.') }}</div>
                        </div>
                        <div>
                            <div style="color:var(--text-muted);margin-bottom:2px;">Diskon</div>
                            <div style="color:var(--text-primary);font-weight:600;">{{ $product->discount }}%</div>
                        </div>
                        <div>
                            <div style="color:var(--text-muted);margin-bottom:2px;">Stok</div>
                            <div style="color:var(--text-primary);font-weight:600;">{{ $product->stock }} unit</div>
                        </div>
                        <div>
                            <div style="color:var(--text-muted);margin-bottom:2px;">Status</div>
                            <span class="badge badge-{{ strtolower($product->status) }}">{{ $product->status }}</span>
                        </div>
                        <div>
                            <div style="color:var(--text-muted);margin-bottom:2px;">Pre-Order</div>
                            <div style="color:var(--text-primary);">{{ $product->is_preorder ? 'Ya' : 'Tidak' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="card mb-4">
                <div class="card-header"><span class="card-title">🎮 Aksi</span></div>
                <div class="card-body action-body">
                    @if ($product->status !== 'APPROVED')
                        <form method="POST" action="{{ route('products.approve', $product) }}">
                            @csrf @method('PATCH')
                            <button class="btn btn-success" style="width:100%; justify-content:center;">✓ Setujui
                                Produk</button>
                        </form>
                    @endif
                    @if ($product->status !== 'REJECTED')
                        <form method="POST" action="{{ route('products.reject', $product) }}">
                            @csrf @method('PATCH')
                            <button class="btn btn-danger" style="width:100%; justify-content:center;">✗ Tolak
                                Produk</button>
                        </form>
                    @endif
                    @if ($product->status !== 'PENDING')
                        <form method="POST" action="{{ route('products.pending', $product) }}">
                            @csrf @method('PATCH')
                            <button class="btn btn-warning" style="width:100%; justify-content:center;">↩ Kembalikan ke
                                Pending</button>
                        </form>
                    @endif
                    @if (auth()->user()->isSuperAdmin())
                        <div class="divider"></div>
                        <form method="POST" action="{{ route('products.destroy', $product) }}"
                            onsubmit="return confirm('Yakin hapus produk ini? Tindakan tidak bisa dibatalkan.')">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger" style="width:100%; justify-content:center;">🗑 Hapus
                                Produk</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right: UMKM & Order Info --}}
        <div>
            <div class="card mb-4">
                <div class="card-header"><span class="card-title">🏪 Informasi UMKM</span></div>
                <div class="card-body">
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
                        <div class="avatar-sm" style="width:48px;height:48px;font-size:18px;">🏪</div>
                        <div>
                            <div style="font-size:16px;font-weight:700;color:var(--text-primary);">
                                {{ $product->umkm->name }}</div>
                            <div style="font-size:13px;color:var(--text-muted);">Owner:
                                {{ $product->umkm->owner->name ?? '-' }}</div>
                        </div>
                        <div style="margin-left:auto;">
                            <a href="{{ route('umkms.show', $product->umkm) }}" class="btn btn-sm btn-secondary">Lihat
                                UMKM</a>
                        </div>
                    </div>
                    <div style="font-size:13px;color:var(--text-muted);">
                        {{ $product->umkm->description ?? 'Tidak ada deskripsi.' }}</div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <span class="card-title">📊 Statistik Penjualan</span>
                </div>
                <div class="card-body" style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;">
                    <div style="text-align:center;padding:16px;background:var(--dark-700);border-radius:10px;">
                        <div style="font-size:24px;font-weight:800;color:var(--text-primary);">
                            {{ $product->orderItems->count() }}</div>
                        <div style="font-size:12px;color:var(--text-muted);margin-top:4px;">Total Terjual</div>
                    </div>
                    <div style="text-align:center;padding:16px;background:var(--dark-700);border-radius:10px;">
                        <div style="font-size:18px;font-weight:800;color:var(--coffee-400);">Rp
                            {{ number_format($product->orderItems->sum('price'), 0, ',', '.') }}</div>
                        <div style="font-size:12px;color:var(--text-muted);margin-top:4px;">Total Revenue</div>
                    </div>
                    <div style="text-align:center;padding:16px;background:var(--dark-700);border-radius:10px;">
                        <div style="font-size:24px;font-weight:800;color:var(--text-primary);">{{ $product->stock }}</div>
                        <div style="font-size:12px;color:var(--text-muted);margin-top:4px;">Sisa Stok</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
