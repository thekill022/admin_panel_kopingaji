@extends('layouts.app')

@section('title', 'Kelola Produk')
@section('page-title', 'Manajemen Produk')
@section('page-subtitle', 'Approve, tolak, dan kelola semua produk')

@section('content')

{{-- Tabs & Search --}}
<div class="card mb-6">
    <div style="padding: 16px 20px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; border-bottom:1px solid var(--dark-600);">
        <div class="tabs">
            @foreach([
                'all'      => ['label' => 'Semua', 'count' => $counts['all']],
                'pending'  => ['label' => 'Pending', 'count' => $counts['pending']],
                'approved' => ['label' => 'Disetujui', 'count' => $counts['approved']],
                'rejected' => ['label' => 'Ditolak', 'count' => $counts['rejected']],
            ] as $key => $tab)
                <a href="{{ route('products.index', ['status' => $key, 'search' => $search]) }}"
                   class="tab-item {{ $status === $key ? 'active' : '' }}">
                    {{ $tab['label'] }}
                    <span class="tab-count">{{ $tab['count'] }}</span>
                </a>
            @endforeach
        </div>
        <form method="GET" action="{{ route('products.index') }}" style="display:flex;gap:8px;align-items:center;">
            <input type="hidden" name="status" value="{{ $status }}" />
            <div class="search-bar" style="width:280px;">
                <span class="search-bar-icon"><i class="fas fa-search"></i></span>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari produk atau UMKM..." />
            </div>
            <button type="submit" class="btn btn-secondary btn-sm">Cari</button>
        </form>
    </div>

    <div class="table-wrap">
        @if($products->isEmpty())
            <div class="empty-state">
                <div class="empty-icon text-gray-200 mb-4 opacity-20"><i class="fas fa-box text-6xl"></i></div>
                <p>Tidak ada produk ditemukan.</p>
            </div>
        @else
        <table>
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>UMKM / Owner</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Status</th>
                    <th>Tgl Dibuat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            @if($product->image_url)
                                <img src="{{ $product->image_url }}" alt="" style="width:44px;height:44px;object-fit:cover;border-radius:8px;flex-shrink:0;" />
                            @else
                                <div style="width:44px;height:44px;background:#f3f4f6;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#9ca3af;">
                                    <i class="fas fa-box"></i>
                                </div>
                            @endif
                            <div>
                                <div class="td-primary">{{ $product->name }}</div>
                                @if($product->is_preorder)
                                    <span style="font-size:10px;background:rgba(139,92,246,0.2);color:#a78bfa;padding:1px 6px;border-radius:4px;">Pre-Order</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="td-primary">{{ $product->umkm->name }}</div>
                        <div class="td-muted">{{ $product->umkm->owner->name ?? '-' }}</div>
                    </td>
                    <td>
                        <div style="color:var(--coffee-400);font-weight:600;">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                        @if($product->discount > 0)
                            <div class="td-muted">Diskon {{ $product->discount }}%</div>
                        @endif
                    </td>
                    <td><span class="td-primary">{{ $product->stock }}</span></td>
                    <td>
                        <span class="badge badge-{{ strtolower($product->status) }}">
                            {{ $product->status }}
                        </span>
                    </td>
                    <td class="td-muted">{{ $product->created_at->format('d/m/Y') }}</td>
                    <td>
                        <div style="display:flex;gap:6px;flex-wrap:wrap;">
                            @if($product->status === 'PENDING' || $product->status === 'REJECTED')
                                <form method="POST" action="{{ route('products.approve', $product) }}">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-xs btn-success">✓ Approve</button>
                                </form>
                            @endif
                            @if($product->status === 'PENDING' || $product->status === 'APPROVED')
                                <form method="POST" action="{{ route('products.reject', $product) }}">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-xs btn-danger">✗ Tolak</button>
                                </form>
                            @endif
                            <a href="{{ route('products.show', $product) }}" class="btn btn-xs btn-secondary">Detail</a>
                            @if(auth()->user()->isSuperAdmin())
                                <form method="POST" action="{{ route('products.destroy', $product) }}"
                                      onsubmit="return confirm('Hapus produk ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-xs btn-danger"><i class="fas fa-trash-alt"></i></button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Pagination --}}
        <div class="pagination-wrap">
            <span>Menampilkan {{ $products->firstItem() }}–{{ $products->lastItem() }} dari {{ $products->total() }} produk</span>
            <div class="pagination">
                @if($products->onFirstPage())
                    <span class="page-link disabled">‹</span>
                @else
                    <a href="{{ $products->previousPageUrl() }}" class="page-link">‹</a>
                @endif

                @foreach($products->getUrlRange(max(1, $products->currentPage()-2), min($products->lastPage(), $products->currentPage()+2)) as $page => $url)
                    <a href="{{ $url }}" class="page-link {{ $page === $products->currentPage() ? 'active' : '' }}">{{ $page }}</a>
                @endforeach

                @if($products->hasMorePages())
                    <a href="{{ $products->nextPageUrl() }}" class="page-link">›</a>
                @else
                    <span class="page-link disabled">›</span>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

@endsection
