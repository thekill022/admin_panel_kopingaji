@extends('layouts.app')

@section('title', 'Manajemen UMKM')
@section('page-title', 'Manajemen UMKM')
@section('page-subtitle', 'Daftar semua bisnis yang terdaftar di platform')

@section('content')

<style>
    .umkm-table { table-layout: fixed; width: 100%; }
    .umkm-table th,
    .umkm-table td { padding: 0.75rem 0.75rem; font-size: 0.8rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; vertical-align: middle; }
    .umkm-table th { font-size: 0.65rem; padding: 0.6rem 0.75rem; }

    .umkm-table .col-name   { width: 28%; }
    .umkm-table .col-owner  { width: 22%; }
    .umkm-table .col-fee    { width: 12%; }
    .umkm-table .col-status { width: 13%; }
    .umkm-table .col-date   { width: 12%; }
    .umkm-table .col-action { width: 13%; }

    .umkm-table .desc-text { font-size: 0.7rem; color: #9ca3af; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .umkm-table .owner-email { font-size: 0.7rem; color: #9ca3af; overflow: hidden; text-overflow: ellipsis; }
    .umkm-action-btns { display: flex; gap: 4px; }
    .umkm-action-btns .btn { font-size: 0.65rem; padding: 0.2rem 0.5rem; }
</style>

    <div class="card">
        <div style="padding:16px 20px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--dark-600);">
            <span style="font-size:13px;color:var(--text-muted);">Total: {{ $umkms->total() }} UMKM</span>
            <form method="GET" style="display:flex;gap:8px;">
                <div class="search-bar" style="width:220px;">
                    <span class="search-bar-icon"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" value="{{ $search }}"
                        placeholder="Cari nama UMKM / owner..." />
                </div>
                <button type="submit" class="btn btn-secondary btn-sm">Cari</button>
            </form>
        </div>

        <div style="overflow-x:hidden;">
            @if ($umkms->isEmpty())
                <div class="empty-state">
                    <div class="empty-icon text-gray-200 mb-4 opacity-20"><i class="fas fa-store text-6xl"></i></div>
                    <p>Tidak ada UMKM ditemukan.</p>
                </div>
            @else
                <table class="umkm-table">
                    <thead>
                        <tr>
                            <th class="col-name">Nama UMKM</th>
                            <th class="col-owner">Owner</th>
                            <th class="col-fee">Fee</th>
                            <th class="col-status">Status</th>
                            <th class="col-date">Bergabung</th>
                            <th class="col-action">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($umkms as $umkm)
                            <tr>
                                <td class="col-name">
                                    <div style="font-weight:700;color:#111827;overflow:hidden;text-overflow:ellipsis;">{{ $umkm->name }}</div>
                                    @if ($umkm->description)
                                        <div class="desc-text">{{ $umkm->description }}</div>
                                    @endif
                                </td>
                                <td class="col-owner">
                                    <div style="font-weight:600;color:#111827;overflow:hidden;text-overflow:ellipsis;font-size:0.8rem;">{{ $umkm->owner->name }}</div>
                                    <div class="owner-email">{{ $umkm->owner->email }}</div>
                                </td>
                                <td class="col-fee">
                                    @if ($umkm->platform_fee_type === 'percentage')
                                        <span style="color:var(--coffee-400);font-weight:700;">{{ $umkm->platform_fee_rate }}%</span>
                                    @else
                                        <span style="color:var(--coffee-400);font-weight:700;font-size:0.75rem;">Rp {{ number_format($umkm->platform_fee_flat, 0, ',', '.') }}</span>
                                    @endif
                                </td>
                                <td class="col-status">
                                    @if ($umkm->is_verified)
                                        <span class="badge badge-success" style="font-size:0.6rem;padding:0.2rem 0.5rem;">Verified</span>
                                    @else
                                        <form method="POST" action="{{ route('umkms.toggleVerify', $umkm) }}">
                                            @csrf @method('PATCH')
                                            <button class="btn btn-xs btn-success" style="font-size:0.6rem;padding:0.2rem 0.5rem;">Verifikasi</button>
                                        </form>
                                    @endif
                                </td>
                                <td class="col-date" style="font-size:0.75rem;color:#9ca3af;">{{ $umkm->created_at->format('d M Y') }}</td>
                                <td class="col-action">
                                    <div class="umkm-action-btns">
                                        <a href="{{ route('umkms.show', $umkm) }}" class="btn btn-xs btn-secondary">Detail</a>
                                        @if (auth()->user()->isSuperAdmin())
                                            <form method="POST" action="{{ route('umkms.destroy', $umkm) }}"
                                                onsubmit="return confirm('Hapus UMKM {{ $umkm->name }}? Semua produknya juga akan terhapus.')">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-xs btn-danger" style="padding:0.2rem 0.4rem;"><i class="fas fa-trash-alt" style="font-size:0.6rem;"></i></button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="pagination-wrap">
                    <span>Menampilkan {{ $umkms->firstItem() }}–{{ $umkms->lastItem() }} dari {{ $umkms->total() }} UMKM</span>
                    <div class="pagination">
                        @if ($umkms->onFirstPage())
                            <span class="page-link disabled">‹</span>
                        @else
                            <a href="{{ $umkms->previousPageUrl() }}" class="page-link">‹</a>
                        @endif
                        @foreach ($umkms->getUrlRange(max(1, $umkms->currentPage() - 2), min($umkms->lastPage(), $umkms->currentPage() + 2)) as $page => $url)
                            <a href="{{ $url }}" class="page-link {{ $page === $umkms->currentPage() ? 'active' : '' }}">{{ $page }}</a>
                        @endforeach
                        @if ($umkms->hasMorePages())
                            <a href="{{ $umkms->nextPageUrl() }}" class="page-link">›</a>
                        @else
                            <span class="page-link disabled">›</span>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

@endsection
