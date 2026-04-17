@extends('layouts.app')

@section('title', 'Manajemen UMKM')
@section('page-title', 'Manajemen UMKM')
@section('page-subtitle', 'Daftar semua bisnis yang terdaftar di platform')

@section('content')

    <div class="card">
        <div
            style="padding:16px 20px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--dark-600);">
            <span style="font-size:13px;color:var(--text-muted);">Total: {{ $umkms->total() }} UMKM</span>
            <form method="GET" style="display:flex;gap:8px;">
                <div class="search-bar" style="width:260px;">
                    <span class="search-bar-icon"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" value="{{ $search }}"
                        placeholder="Cari nama UMKM / owner..." />
                </div>
                <button type="submit" class="btn btn-secondary btn-sm">Cari</button>
            </form>
        </div>

        <div class="table-wrap">
            @if ($umkms->isEmpty())
                <div class="empty-state">
                    <div class="empty-icon text-gray-200 mb-4 opacity-20"><i class="fas fa-store text-6xl"></i></div>
                    <p>Tidak ada UMKM ditemukan.</p>
                </div>
            @else
                <table>
                    <thead>
                        <tr>
                            <th>Nama UMKM</th>
                            <th>Owner</th>
                            <th>Platform Fee</th>
                            <th>Status</th>
                            <th>Bergabung</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($umkms as $umkm)
                            <tr>
                                <td>
                                    <div class="td-primary">{{ $umkm->name }}</div>
                                    @if ($umkm->description)
                                        <div class="td-muted"
                                            style="max-width:240px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                            {{ $umkm->description }}</div>
                                    @endif
                                </td>
                                <td>
                                    <div class="td-primary">{{ $umkm->owner->name }}</div>
                                    <div class="td-muted">{{ $umkm->owner->email }}</div>
                                </td>
                                <td>
                                    @if ($umkm->platform_fee_type === 'percentage')
                                        <span
                                            style="color:var(--coffee-400);font-weight:700;">{{ $umkm->platform_fee_rate }}%</span>
                                    @else
                                        <span style="color:var(--coffee-400);font-weight:700;">Rp
                                            {{ number_format($umkm->platform_fee_flat, 0, ',', '.') }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($umkm->is_verified)
                                        <span class="badge badge-success">Terverifikasi</span>
                                    @else
                                        <form method="POST" action="{{ route('umkms.toggleVerify', $umkm) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button class="btn btn-xs btn-success">Verifikasi</button>
                                        </form>
                                    @endif
                                </td>
                                <td class="td-muted">{{ $umkm->created_at->format('d M Y') }}</td>
                                <td>
                                    <div style="display:flex;gap:6px;">
                                        <a href="{{ route('umkms.show', $umkm) }}"
                                            class="btn btn-xs btn-secondary">Detail</a>
                                        @if (auth()->user()->isSuperAdmin())
                                            <form method="POST" action="{{ route('umkms.destroy', $umkm) }}"
                                                onsubmit="return confirm('Hapus UMKM {{ $umkm->name }}? Semua produknya juga akan terhapus.')">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-xs btn-danger"><i
                                                        class="fas fa-trash-alt"></i></button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="pagination-wrap">
                    <span>Menampilkan {{ $umkms->firstItem() }}–{{ $umkms->lastItem() }} dari {{ $umkms->total() }}
                        UMKM</span>
                    <div class="pagination">
                        @if ($umkms->onFirstPage())
                            <span class="page-link disabled">‹</span>
                        @else
                            <a href="{{ $umkms->previousPageUrl() }}" class="page-link">‹</a>
                        @endif
                        @foreach ($umkms->getUrlRange(max(1, $umkms->currentPage() - 2), min($umkms->lastPage(), $umkms->currentPage() + 2)) as $page => $url)
                            <a href="{{ $url }}"
                                class="page-link {{ $page === $umkms->currentPage() ? 'active' : '' }}">{{ $page }}</a>
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
