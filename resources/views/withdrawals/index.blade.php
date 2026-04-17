@extends('layouts.app')

@section('title', 'Manajemen Penarikan')
@section('page-title', 'Manajemen Penarikan')
@section('page-subtitle', 'Setujui atau tolak permintaan penarikan owner')

@section('content')

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
            <div class="search-bar" style="width:240px;">
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
        <table>
            <thead>
                <tr>
                    <th>#ID</th>
                    <th>Owner / UMKM</th>
                    <th>Bruto</th>
                    <th>Komisi Platform</th>
                    <th>Biaya DOKU</th>
                    <th>Diterima (Neto)</th>
                    <th>Rekening Tujuan</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($withdrawals as $wd)
                <tr>
                    <td><span style="font-family:monospace;color:var(--coffee-400);">#{{ $wd->id }}</span></td>
                    <td>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div class="avatar-sm">{{ strtoupper(substr($wd->owner->name,0,1)) }}</div>
                            <div>
                                <div class="td-primary">{{ $wd->owner->name }}</div>
                                <div class="td-muted">{{ $wd->owner->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="color:var(--coffee-400);font-weight:700;">
                        Rp {{ number_format($wd->gross_amount ?: $wd->amount, 0, ',', '.') }}
                    </td>
                    <td>
                        @if($wd->platform_fee_deduction > 0)
                            <span style="color:#f97316;font-weight:600;">- Rp {{ number_format($wd->platform_fee_deduction,0,',','.') }}</span>
                        @else
                            <span class="td-muted" title="Di bawah threshold / tidak dikonfigurasi">—</span>
                        @endif
                    </td>
                    <td>
                        @if($wd->admin_fee_amount > 0)
                            <span style="color:#ef4444;font-weight:600;">- Rp {{ number_format($wd->admin_fee_amount,0,',','.') }}</span>
                        @else
                            <span class="td-muted">—</span>
                        @endif
                    </td>
                    <td style="color:#16a34a;font-weight:700;">
                        Rp {{ number_format($wd->net_disbursed ?: $wd->amount, 0, ',', '.') }}
                    </td>
                    <td>
                        <div class="td-primary" style="font-weight:600;">{{ $wd->account_name ?? '—' }}</div>
                        <div style="font-size:12px;color:#9ca3af;">
                            {{ $wd->bank_name ?? '' }}
                            @if($wd->bank_code)
                                <span style="font-family:monospace;color:#6366f1;">({{ $wd->bank_code }})</span>
                            @endif
                        </div>
                        <div style="font-size:12px;color:#374151;font-family:monospace;">{{ $wd->bank_account ?? '—' }}</div>
                    </td>
                    <td><span class="badge badge-{{ strtolower($wd->status) }}">{{ $wd->status }}</span></td>
                    <td class="td-muted">{{ $wd->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            @if($wd->status === 'PENDING')
                                <form method="POST" action="{{ route('withdrawals.approve', $wd) }}">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-xs btn-success">✓ Setujui</button>
                                </form>
                                <form method="POST" action="{{ route('withdrawals.reject', $wd) }}">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-xs btn-danger">✗ Tolak</button>
                                </form>
                            @else
                                <span class="td-muted text-xs">—</span>
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
