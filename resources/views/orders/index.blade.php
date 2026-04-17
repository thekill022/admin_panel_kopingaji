@extends('layouts.app')

@section('title', 'Manajemen Pesanan')
@section('page-title', 'Manajemen Pesanan')
@section('page-subtitle', 'Pantau dan kelola semua transaksi')

@section('content')

<style>
    /* ── Scoped: compact order table ── */
    .order-table { table-layout: fixed; width: 100%; }
    .order-table th,
    .order-table td { padding: 0.75rem 0.75rem; font-size: 0.8rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .order-table th { font-size: 0.65rem; padding: 0.6rem 0.75rem; }

    /* Column widths */
    .order-table .col-id      { width: 50px; }
    .order-table .col-buyer   { width: 22%; }
    .order-table .col-umkm    { width: 16%; }
    .order-table .col-total   { width: 12%; }
    .order-table .col-method  { width: 12%; }
    .order-table .col-status  { width: 11%; }
    .order-table .col-date    { width: 13%; }
    .order-table .col-action  { width: 70px; }

    .order-table .td-buyer-email { font-size: 0.7rem; color: #9ca3af; overflow: hidden; text-overflow: ellipsis; }
    .order-table .td-umkm-name { white-space: normal; line-height: 1.3; }

    @media (max-width: 768px) {
        .order-table .col-id, .order-table .col-method, .order-table .col-date { display: none; }
        .order-table .col-buyer { width: 33%; }
        .order-table .col-umkm { width: 25%; }
        .order-table .col-total { width: 17%; }
        .order-table .col-status { width: 10%; }
        .order-table .col-action { width: 15%; }
        .order-table th, .order-table td { font-size: 0.7rem; padding: 0.4rem 0.5rem; }
    }
</style>

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
            <div class="search-bar" style="width:220px;">
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
        <table class="order-table">
            <thead>
                <tr>
                    <th class="col-id">#ID</th>
                    <th class="col-buyer">Pembeli</th>
                    <th class="col-umkm">UMKM</th>
                    <th class="col-total">Total</th>
                    <th class="col-method">Metode</th>
                    <th class="col-status">Status</th>
                    <th class="col-date">Tanggal</th>
                    <th class="col-action">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                <tr>
                    <td class="col-id"><span style="font-family:monospace;color:var(--coffee-400);font-weight:700;">#{{ $order->id }}</span></td>
                    <td class="col-buyer">
                        <div class="td-primary" style="font-size:0.8rem;overflow:hidden;text-overflow:ellipsis;">{{ $order->buyer->name }}</div>
                        <div class="td-buyer-email">{{ $order->buyer->email }}</div>
                    </td>
                    <td class="col-umkm">
                        <span class="td-primary td-umkm-name" style="font-size:0.8rem;">{{ $order->umkm->name }}</span>
                    </td>
                    <td class="col-total" style="color:var(--coffee-400);font-weight:700;">Rp {{ number_format($order->total_price,0,',','.') }}</td>
                    <td class="col-method">
                        <span class="badge {{ $order->payment_method === 'NON_CASH' ? 'badge-paid' : 'badge-buyer' }}" style="font-size:0.6rem;padding:0.2rem 0.5rem;">
                            {{ $order->payment_method ?? '—' }}
                        </span>
                    </td>
                    <td class="col-status"><span class="badge badge-{{ strtolower($order->status) }}" style="font-size:0.6rem;padding:0.2rem 0.5rem;">{{ $order->status }}</span></td>
                    <td class="col-date td-muted" style="font-size:0.75rem;">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                    <td class="col-action">
                        <a href="{{ route('orders.show', $order) }}" class="btn btn-xs btn-secondary" style="font-size:0.65rem;padding:0.2rem 0.5rem;">Detail</a>
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
