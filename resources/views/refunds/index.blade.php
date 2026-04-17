@extends('layouts.app')

@section('title', 'Pengajuan Refund')
@section('page-title', 'Pengajuan Refund')
@section('page-subtitle', 'Kelola permintaan pengembalian dana dari pembeli')

@section('content')

<style>
    .refund-tbl { table-layout: fixed; width: 100%; }
    .refund-tbl th,
    .refund-tbl td {
        padding: 0.75rem 0.75rem;
        font-size: 0.8rem;
        vertical-align: middle;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .refund-tbl th { font-size: 0.65rem; padding: 0.6rem 0.75rem; }

    /* Total 100% */
    .refund-tbl .col-buyer  { width: 18%; }
    .refund-tbl .col-umkm   { width: 15%; }
    .refund-tbl .col-amount { width: 14%; }
    .refund-tbl .col-reason { width: 18%; }
    .refund-tbl .col-role   { width: 8%; text-align: center; }
    .refund-tbl .col-status { width: 9%; text-align: center; }
    .refund-tbl .col-action { width: 18%; overflow: visible; white-space: normal; text-align: right; }

    .refund-tbl .action-flex {
        display: flex;
        gap: 5px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }
    .refund-tbl .action-flex .btn { font-size: 0.6rem; padding: 0.2rem 0.5rem; white-space: nowrap; }

    .refund-tbl .reason-text {
        font-size: 0.75rem;
        color: #4b5563;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        white-space: normal;
        line-height: 1.35;
    }

    @media (max-width: 768px) {
        .refund-tbl .col-buyer, .refund-tbl .col-reason, .refund-tbl .col-role { display: none; }
        .refund-tbl .col-umkm { width: 33%; }
        .refund-tbl .col-amount { width: 25%; }
        .refund-tbl .col-status { width: 17%; }
        .refund-tbl .col-action { width: 25%; }
        .refund-tbl th, .refund-tbl td { font-size: 0.7rem; padding: 0.4rem 0.5rem; }
    }
</style>

<div class="card">
    {{-- Tabs + Search --}}
    <div style="padding:16px 20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;border-bottom:1px solid #f3f4f6;">
        <div class="tabs">
            @foreach([
                'all'=>['label'=>'Semua','count'=>$counts['all']],
                'pending'=>['label'=>'Pending','count'=>$counts['pending']],
                'approved'=>['label'=>'Disetujui','count'=>$counts['approved']],
                'rejected'=>['label'=>'Ditolak','count'=>$counts['rejected']],
            ] as $key=>$tab)
                <a href="{{ route('refunds.index', ['status'=>$key,'search'=>$search]) }}"
                   class="tab-item {{ $status===$key?'active':'' }}">
                    {{ $tab['label'] }} <span class="tab-count">{{ $tab['count'] }}</span>
                </a>
            @endforeach
        </div>
        <form method="GET" style="display:flex;gap:8px;">
            <input type="hidden" name="status" value="{{ $status }}" />
            <div class="search-bar" style="width:220px;">
                <span class="search-bar-icon"><i class="fas fa-search"></i></span>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari pembeli, UMKM..." />
            </div>
            <button type="submit" class="btn btn-secondary btn-sm">Cari</button>
        </form>
    </div>

    {{-- Table --}}
    <div class="table-wrap">
        @if($refunds->isEmpty())
            <div class="empty-state">
                <div class="empty-icon" style="font-size:3rem;opacity:0.15;margin-bottom:0.75rem;"><i class="fas fa-undo-alt"></i></div>
                <p>Tidak ada pengajuan refund ditemukan.</p>
            </div>
        @else
        <table class="refund-tbl">
            <thead>
                <tr>
                    <th class="col-buyer">Pembeli</th>
                    <th class="col-umkm">UMKM & Order</th>
                    <th class="col-amount">Jumlah Refund</th>
                    <th class="col-reason">Alasan</th>
                    <th class="col-role">Oleh</th>
                    <th class="col-status">Status</th>
                    <th class="col-action">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($refunds as $refund)
                <tr>
                    <td class="col-buyer">
                        <div style="font-weight:700;color:#111827;overflow:hidden;text-overflow:ellipsis;">{{ $refund->order->buyer->name ?? '-' }}</div>
                        <div style="font-size:0.7rem;color:#9ca3af;overflow:hidden;text-overflow:ellipsis;">{{ $refund->order->buyer->email ?? '' }}</div>
                    </td>
                    <td class="col-umkm">
                        <div style="font-weight:700;color:#111827;overflow:hidden;text-overflow:ellipsis;">{{ $refund->order->umkm->name ?? '-' }}</div>
                        <a href="{{ route('orders.show', $refund->order_id) }}" style="font-family:monospace;font-size:0.75rem;color:var(--coffee-400);font-weight:700;text-decoration:none;">
                            #{{ $refund->order_id }}
                        </a>
                    </td>
                    <td class="col-amount">
                        <div style="color:#dc2626;font-weight:700;">Rp {{ number_format($refund->amount, 0, ',', '.') }}</div>
                        <div style="font-size:0.7rem;color:#9ca3af;">{{ $refund->created_at->format('d/m/Y') }}</div>
                    </td>
                    <td class="col-reason">
                        <div class="reason-text" title="{{ $refund->reason }}">
                            {{ $refund->reason }}
                        </div>
                    </td>
                    <td class="col-role">
                        @if($refund->requested_by === 'BUYER')
                            <span class="badge badge-buyer" style="font-size:0.6rem;padding:0.2rem 0.5rem;">Pembeli</span>
                        @else
                            <span class="badge badge-owner" style="font-size:0.6rem;padding:0.2rem 0.5rem;">Penjual</span>
                        @endif
                    </td>
                    <td class="col-status">
                        @if($refund->status === 'PENDING')
                            <span class="badge badge-pending" style="font-size:0.6rem;padding:0.2rem 0.5rem;">Pending</span>
                        @elseif($refund->status === 'APPROVED')
                            <span class="badge badge-approved" style="font-size:0.6rem;padding:0.2rem 0.5rem;">Approved</span>
                        @else
                            <span class="badge badge-rejected" style="font-size:0.6rem;padding:0.2rem 0.5rem;">Rejected</span>
                        @endif
                    </td>
                    <td class="col-action">
                        <div class="action-flex">
                            @if($refund->status === 'PENDING')
                                <form method="POST" action="{{ route('refunds.approve', $refund) }}">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-success">✓ Approve</button>
                                </form>
                                <form method="POST" action="{{ route('refunds.reject', $refund) }}">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-warning">✗ Tolak</button>
                                </form>
                            @endif
                            <a href="{{ route('refunds.show', $refund) }}" class="btn btn-secondary">Detail</a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Pagination --}}
        @if($refunds->hasPages())
        <div class="pagination-wrap">
            <span>Menampilkan {{ $refunds->firstItem() }}–{{ $refunds->lastItem() }} dari {{ $refunds->total() }}</span>
            <div class="pagination">
                @if($refunds->onFirstPage())
                    <span class="page-link disabled">‹</span>
                @else
                    <a href="{{ $refunds->previousPageUrl() }}" class="page-link">‹</a>
                @endif
                @foreach($refunds->getUrlRange(max(1,$refunds->currentPage()-2), min($refunds->lastPage(),$refunds->currentPage()+2)) as $page => $url)
                    <a href="{{ $url }}" class="page-link {{ $page === $refunds->currentPage() ? 'active' : '' }}">{{ $page }}</a>
                @endforeach
                @if($refunds->hasMorePages())
                    <a href="{{ $refunds->nextPageUrl() }}" class="page-link">›</a>
                @else
                    <span class="page-link disabled">›</span>
                @endif
            </div>
        </div>
        @endif
        @endif
    </div>
</div>

@endsection
