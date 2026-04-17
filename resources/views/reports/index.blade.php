@extends('layouts.app')

@section('title', 'Laporan Pengguna')
@section('page-title', 'Laporan Pengguna')
@section('page-subtitle', 'Tinjau dan kelola laporan aktivitas mencurigakan dari pengguna')

@section('content')

<style>
    .report-table { table-layout: fixed; width: 100%; }
    .report-table th,
    .report-table td { padding: 0.7rem 0.7rem; font-size: 0.78rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; vertical-align: middle; }
    .report-table th { font-size: 0.6rem; padding: 0.55rem 0.7rem; }

    .report-table .col-id       { width: 40px; text-align: center; }
    .report-table .col-reporter { width: 17%; }
    .report-table .col-target   { width: 18%; }
    .report-table .col-cat      { width: 14%; }
    .report-table .col-desc     { width: 22%; }
    .report-table .col-status   { width: 9%; }
    .report-table .col-date     { width: 11%; }
    .report-table .col-action   { width: 80px; text-align: center; overflow: visible; }

    .report-table .reporter-name { font-weight: 600; font-size: 0.78rem; color: #111827; overflow: hidden; text-overflow: ellipsis; }
    .report-table .reporter-email { font-size: 0.65rem; color: #9ca3af; overflow: hidden; text-overflow: ellipsis; }
    .report-table .target-label { font-size: 0.6rem; font-weight: 700; text-transform: uppercase; margin-bottom: 1px; }
    .report-table .target-name { font-weight: 600; font-size: 0.78rem; color: #111827; overflow: hidden; text-overflow: ellipsis; }
    .report-table .target-sub { font-size: 0.65rem; color: #9ca3af; overflow: hidden; text-overflow: ellipsis; }
    .report-table .desc-clamp {
        font-size: 0.75rem;
        color: #4b5563;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        white-space: normal;
        line-height: 1.35;
        margin: 0;
    }

    @media (max-width: 768px) {
        .report-table .col-id, .report-table .col-cat, .report-table .col-date { display: none; }
        .report-table .col-reporter { width: 25%; }
        .report-table .col-target { width: 25%; }
        .report-table .col-desc { width: 25%; }
        .report-table .col-status { width: 13%; }
        .report-table .col-action { width: 12%; }
        .report-table th, .report-table td { font-size: 0.7rem; padding: 0.4rem 0.5rem; }
    }
</style>

<div class="card">
    {{-- Card Header: Tabs + Search --}}
    <div style="padding:16px 20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;border-bottom:1px solid #f3f4f6;">
        <div class="tabs">
            @foreach([
                'all'       => ['label' => 'Semua',     'count' => $counts['all']],
                'pending'   => ['label' => 'Pending',   'count' => $counts['pending']],
                'reviewed'  => ['label' => 'Reviewed',  'count' => $counts['reviewed']],
                'dismissed' => ['label' => 'Dismissed', 'count' => $counts['dismissed']],
            ] as $key => $tab)
                <a href="{{ route('reports.index', ['status' => $key, 'search' => $search]) }}"
                   class="tab-item {{ $status === $key ? 'active' : '' }}">
                    {{ $tab['label'] }} <span class="tab-count">{{ $tab['count'] }}</span>
                </a>
            @endforeach
        </div>

        <form method="GET" action="{{ route('reports.index') }}" style="display:flex;gap:8px;align-items:center;">
            <input type="hidden" name="status" value="{{ $status }}">
            <div class="search-bar" style="width:200px;">
                <i class="fas fa-search search-bar-icon"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari pelapor, UMKM...">
            </div>
            <button type="submit" class="btn btn-secondary btn-sm">Cari</button>
        </form>
    </div>

    {{-- Table --}}
    <div class="table-wrap">
        @if($reports->isEmpty())
            <div class="empty-state">
                <i class="fas fa-flag empty-icon"></i>
                <p>Tidak ada laporan ditemukan</p>
            </div>
        @else
            <table class="report-table">
                <thead>
                    <tr>
                        <th class="col-id">ID</th>
                        <th class="col-reporter">Pelapor</th>
                        <th class="col-target">Target</th>
                        <th class="col-cat">Kategori</th>
                        <th class="col-desc">Deskripsi</th>
                        <th class="col-status">Status</th>
                        <th class="col-date">Tanggal</th>
                        <th class="col-action">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reports as $report)
                        <tr>
                            <td class="col-id" style="text-align:center;">
                                <span style="font-family:monospace;color:#c4811f;font-weight:700;font-size:0.8rem;">{{ $report->id }}</span>
                            </td>

                            <td class="col-reporter">
                                <div class="reporter-name">{{ $report->reporter->name ?? '-' }}</div>
                                <div class="reporter-email">{{ $report->reporter->email ?? '' }}</div>
                            </td>

                            <td class="col-target">
                                @if($report->product)
                                    <div class="target-label" style="color:#d97706;">Produk</div>
                                    <div class="target-name">{{ Str::limit($report->product->name, 24) }}</div>
                                    <div class="target-sub">{{ $report->umkm->name ?? '' }}</div>
                                @elseif($report->umkm)
                                    <div class="target-label" style="color:#4f46e5;">UMKM</div>
                                    <div class="target-name">{{ $report->umkm->name }}</div>
                                @else
                                    <span style="font-size:0.7rem;color:#d1d5db;">Dihapus</span>
                                @endif
                            </td>

                            <td class="col-cat">
                                <span class="badge" style="background:#f3f4f6;color:#374151;font-size:0.55rem;padding:0.15rem 0.4rem;white-space:normal;line-height:1.3;display:inline-block;text-align:center;">
                                    {{ \App\Models\Report::$categories[$report->category] ?? $report->category }}
                                </span>
                            </td>

                            <td class="col-desc">
                                <p class="desc-clamp" title="{{ $report->description }}">{{ $report->description }}</p>
                            </td>

                            <td class="col-status">
                                @if($report->status === 'PENDING')
                                    <span class="badge badge-pending" style="font-size:0.55rem;padding:0.15rem 0.4rem;">Pending</span>
                                @elseif($report->status === 'REVIEWED')
                                    <span class="badge badge-approved" style="font-size:0.55rem;padding:0.15rem 0.4rem;">Reviewed</span>
                                @else
                                    <span class="badge" style="background:#f3f4f6;color:#6b7280;font-size:0.55rem;padding:0.15rem 0.4rem;">Dismissed</span>
                                @endif
                            </td>

                            <td class="col-date">
                                <div style="font-size:0.72rem;color:#111827;">{{ $report->created_at->format('d/m/Y') }}</div>
                                <div style="font-size:0.62rem;color:#9ca3af;">{{ $report->created_at->format('H:i') }}</div>
                            </td>

                            <td class="col-action" style="text-align:center;">
                                <a href="{{ route('reports.show', $report) }}" class="btn btn-xs btn-secondary" style="font-size:0.6rem;padding:0.2rem 0.5rem;">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Pagination --}}
            @if($reports->hasPages())
                <div class="pagination-wrap">
                    <span>Menampilkan {{ $reports->firstItem() }}–{{ $reports->lastItem() }} dari {{ $reports->total() }}</span>
                    <div class="pagination">
                        @if($reports->onFirstPage())
                            <span class="page-link disabled">‹</span>
                        @else
                            <a href="{{ $reports->previousPageUrl() }}" class="page-link">‹</a>
                        @endif
                        @foreach($reports->getUrlRange(max(1,$reports->currentPage()-2), min($reports->lastPage(),$reports->currentPage()+2)) as $page => $url)
                            <a href="{{ $url }}" class="page-link {{ $page === $reports->currentPage() ? 'active' : '' }}">{{ $page }}</a>
                        @endforeach
                        @if($reports->hasMorePages())
                            <a href="{{ $reports->nextPageUrl() }}" class="page-link">›</a>
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
