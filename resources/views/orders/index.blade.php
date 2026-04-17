@extends('layouts.app')

@section('title', 'Manajemen Pesanan')
@section('page-title', 'Manajemen Pesanan')
@section('page-subtitle', 'Pantau dan kelola semua transaksi')

@section('content')

<div class="card mb-6">
    <div style="padding:16px 20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;border-bottom:1px solid var(--dark-600);">
        <div class="tabs">
            @foreach([
                'all'       => ['label'=>'Semua', 'count'=>$counts['all']],
                'pending'   => ['label'=>'Pending', 'count'=>$counts['pending']],
                'paid'      => ['label'=>'Dibayar', 'count'=>$counts['paid']],
                'completed' => ['label'=>'Selesai', 'count'=>$counts['completed']],
                'cancelled' => ['label'=>'Dibatalkan', 'count'=>$counts['cancelled']],
            ] as $key => $tab)
                <a href="{{ route('orders.index', ['status'=>$key,'search'=>$search]) }}"
                   class="tab-item {{ $status === $key ? 'active' : '' }}">
                    {{ $tab['label'] }} <span class="tab-count">{{ $tab['count'] }}</span>
                </a>
            @endforeach
        </div>
        <form method="GET" style="display:flex;gap:8px;">
            <input type="hidden" name="status" value="{{ $status }}" />
            <div class="search-bar" style="width:260px;">
                <span class="search-bar-icon">🔍</span>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari order / pembeli..." />
            </div>
            <button type="submit" class="btn btn-secondary btn-sm">Cari</button>
        </form>
    </div>

    <div class="table-wrap">
        @if($orders->isEmpty())
            <div class="empty-state"><div class="empty-icon">🛒</div><p>Tidak ada pesanan ditemukan.</p></div>
        @else
        <table>
            <thead>
                <tr>
                    <th>#ID</th>
                    <th>Pembeli</th>
                    <th>UMKM</th>
                    <th>Total</th>
                    <th>Metode Bayar</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                <tr>
                    <td><span style="font-family:monospace;color:var(--coffee-400);">#{{ $order->id }}</span></td>
                    <td>
                        <div class="td-primary">{{ $order->buyer->name }}</div>
                        <div class="td-muted">{{ $order->buyer->email }}</div>
                    </td>
                    <td class="td-primary">{{ $order->umkm->name }}</td>
                    <td style="color:var(--coffee-400);font-weight:700;">Rp {{ number_format($order->total_price,0,',','.') }}</td>
                    <td>
                        <span class="badge {{ $order->payment_method === 'NON_CASH' ? 'badge-paid' : 'badge-buyer' }}">
                            {{ $order->payment_method }}
                        </span>
                    </td>
                    <td><span class="badge badge-{{ strtolower($order->status) }}">{{ $order->status }}</span></td>
                    <td class="td-muted">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <a href="{{ route('orders.show', $order) }}" class="btn btn-xs btn-secondary">Detail</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="pagination-wrap">
            <span>Menampilkan {{ $orders->firstItem() }}–{{ $orders->lastItem() }} dari {{ $orders->total() }} pesanan</span>
            <div class="pagination">
                @if($orders->onFirstPage())
                    <span class="page-link disabled">‹</span>
                @else
                    <a href="{{ $orders->previousPageUrl() }}" class="page-link">‹</a>
                @endif
                @foreach($orders->getUrlRange(max(1,$orders->currentPage()-2),min($orders->lastPage(),$orders->currentPage()+2)) as $page=>$url)
                    <a href="{{ $url }}" class="page-link {{ $page===$orders->currentPage()?'active':'' }}">{{ $page }}</a>
                @endforeach
                @if($orders->hasMorePages())
                    <a href="{{ $orders->nextPageUrl() }}" class="page-link">›</a>
                @else
                    <span class="page-link disabled">›</span>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

@endsection
