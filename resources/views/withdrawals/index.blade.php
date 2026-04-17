@extends('layouts.app')

@section('title', 'Manajemen Penarikan')
@section('page-title', 'Manajemen Penarikan')
@section('page-subtitle', 'Setujui atau tolak permintaan penarikan owner')

@section('content')

<style>
    .wd-table { table-layout: fixed; width: 100%; }
    .wd-table th,
    .wd-table td { padding: 0.6rem 0.6rem; font-size: 0.78rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; vertical-align: middle; }
    .wd-table th { font-size: 0.6rem; padding: 0.5rem 0.6rem; }

    .wd-table .col-id      { width: 42px; }
    .wd-table .col-owner   { width: 18%; }
    .wd-table .col-bruto   { width: 11%; }
    .wd-table .col-komisi  { width: 10%; }
    .wd-table .col-doku    { width: 9%; }
    .wd-table .col-neto    { width: 11%; }
    .wd-table .col-rek     { width: 17%; }
    .wd-table .col-status  { width: 8%; }
    .wd-table .col-date    { width: 10%; }
    .wd-table .col-action  { width: 90px; }

    .wd-table .rek-detail { white-space: normal; line-height: 1.35; }
    .wd-table .owner-info { overflow: hidden; }
    .wd-table .owner-name { font-weight: 700; color: #111827; overflow: hidden; text-overflow: ellipsis; font-size: 0.78rem; }
    .wd-table .owner-email { font-size: 0.65rem; color: #9ca3af; overflow: hidden; text-overflow: ellipsis; }
    .wd-action-btns { display: flex; gap: 4px; }
    .wd-action-btns .btn { font-size: 0.6rem; padding: 0.2rem 0.4rem; white-space: nowrap; }

    @media (max-width: 768px) {
        .wd-table .col-id, .wd-table .col-bruto, .wd-table .col-komisi, .wd-table .col-doku, .wd-table .col-date { display: none; }
        .wd-table .col-owner { width: 25%; }
        .wd-table .col-neto { width: 25%; }
        .wd-table .col-rek { width: 25%; }
        .wd-table .col-status { width: 12%; }
        .wd-table .col-action { width: 13%; }
    }
</style>

<div class="card">
    <div style="padding:16px 20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;border-bottom:1px solid var(--dark-600);">
        <div class="tabs">
            @foreach([
                'all'=>['label'=>'Semua','count'=>$counts['all']],
                'pending'=>['label'=>'Pending','count'=>$counts['pending']],
                'approved'=>['label'=>'Disetujui','count'=>$counts['approved']],
                'rejected'=>['label'=>'Ditolak','count'=>$counts['rejected']],
            ] as $key=>$tab)
                <a href="{{ route('withdrawals.index', ['status'=>$key,'search'=>$search]) }}"
                   class="tab-item {{ $status===$key?'active':'' }}">
                    {{ $tab['label'] }} <span class="tab-count">{{ $tab['count'] }}</span>
                </a>
            @endforeach
        </div>
        <form method="GET" style="display:flex;gap:8px;">
            <input type="hidden" name="status" value="{{ $status }}" />
            <div class="search-bar" style="width:200px;">
                <span class="search-bar-icon">🔍</span>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama / bank..." />
            </div>
            <button type="submit" class="btn btn-secondary btn-sm">Cari</button>
        </form>
    </div>

    <div class="table-wrap">
        @if($withdrawals->isEmpty())
            <div class="empty-state"><div class="empty-icon">💸</div><p>Tidak ada permintaan penarikan.</p></div>
        @else
        <table class="wd-table">
            <thead>
                <tr>
                    <th class="col-id">#</th>
                    <th class="col-owner">Owner</th>
                    <th class="col-bruto">Bruto</th>
                    <th class="col-komisi">Komisi</th>
                    <th class="col-doku">Adm</th>
                    <th class="col-neto">Neto</th>
                    <th class="col-rek">Rekening</th>
                    <th class="col-status">Status</th>
                    <th class="col-date">Tanggal</th>
                    <th class="col-action">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($withdrawals as $wd)
                <tr>
                    <td class="col-id"><span style="font-family:monospace;color:var(--coffee-400);font-weight:700;">#{{ $wd->id }}</span></td>
                    <td class="col-owner">
                        <div class="owner-info">
                            <div class="owner-name">{{ $wd->owner->name }}</div>
                            <div class="owner-email">{{ $wd->owner->email }}</div>
                        </div>
                    </td>
                    <td class="col-bruto" style="color:var(--coffee-400);font-weight:700;font-size:0.75rem;">
                        Rp {{ number_format($wd->gross_amount ?: $wd->amount, 0, ',', '.') }}
                    </td>
                    <td class="col-komisi">
                        @if($wd->platform_fee_deduction > 0)
                            <span style="color:#f97316;font-weight:600;font-size:0.75rem;">-{{ number_format($wd->platform_fee_deduction,0,',','.') }}</span>
                        @else
                            <span style="color:#d1d5db;">—</span>
                        @endif
                    </td>
                    <td class="col-doku">
                        @if($wd->admin_fee_amount > 0)
                            <span style="color:#ef4444;font-weight:600;font-size:0.75rem;">-{{ number_format($wd->admin_fee_amount,0,',','.') }}</span>
                        @else
                            <span style="color:#d1d5db;">—</span>
                        @endif
                    </td>
                    <td class="col-neto" style="color:#16a34a;font-weight:700;font-size:0.75rem;">
                        Rp {{ number_format($wd->net_disbursed ?: $wd->amount, 0, ',', '.') }}
                    </td>
                    <td class="col-rek">
                        <div class="rek-detail">
                            <div style="font-weight:600;color:#111827;font-size:0.75rem;overflow:hidden;text-overflow:ellipsis;">{{ $wd->account_name ?? '—' }}</div>
                            <div style="font-size:0.65rem;color:#9ca3af;">
                                {{ $wd->bank_name ?? '' }}
                                @if($wd->bank_code)
                                    <span style="color:#6366f1;">({{ $wd->bank_code }})</span>
                                @endif
                            </div>
                            <div style="font-size:0.65rem;color:#374151;font-family:monospace;">{{ $wd->bank_account ?? '—' }}</div>
                        </div>
                    </td>
                    <td class="col-status"><span class="badge badge-{{ strtolower($wd->status) }}" style="font-size:0.55rem;padding:0.15rem 0.4rem;">{{ $wd->status }}</span></td>
                    <td class="col-date" style="font-size:0.7rem;color:#9ca3af;">{{ $wd->created_at->format('d/m/Y H:i') }}</td>
                    <td class="col-action">
                        <div class="wd-action-btns">
                            @if($wd->status === 'PENDING')
                                <form method="POST" action="{{ route('withdrawals.approve', $wd) }}">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-xs btn-success">✓</button>
                                </form>
                                <form method="POST" action="{{ route('withdrawals.reject', $wd) }}">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-xs btn-danger">✗</button>
                                </form>
                            @else
                                <span style="color:#d1d5db;font-size:0.7rem;">—</span>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="pagination-wrap">
            <span>Menampilkan {{ $withdrawals->firstItem() }}–{{ $withdrawals->lastItem() }} dari {{ $withdrawals->total() }}</span>
            <div class="pagination">
                @if($withdrawals->onFirstPage())
                    <span class="page-link disabled">‹</span>
                @else
                    <a href="{{ $withdrawals->previousPageUrl() }}" class="page-link">‹</a>
                @endif
                @foreach($withdrawals->getUrlRange(max(1,$withdrawals->currentPage()-2),min($withdrawals->lastPage(),$withdrawals->currentPage()+2)) as $page=>$url)
                    <a href="{{ $url }}" class="page-link {{ $page===$withdrawals->currentPage()?'active':'' }}">{{ $page }}</a>
                @endforeach
                @if($withdrawals->hasMorePages())
                    <a href="{{ $withdrawals->nextPageUrl() }}" class="page-link">›</a>
                @else
                    <span class="page-link disabled">›</span>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

@endsection
