@extends('layouts.app')

@section('title', 'Laporan Pengguna')
@section('page-title', 'Laporan Pengguna')
@section('page-subtitle', 'Tinjau dan kelola laporan aktivitas mencurigakan dari pengguna')

@section('content')

<div class="card">
    {{-- Card Header: Tabs + Search --}}
    <div style="padding:16px 20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;border-bottom:1px solid #f3f4f6;">
        {{-- Status Tabs --}}
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

        {{-- Search --}}
        <form method="GET" action="{{ route('reports.index') }}" style="display:flex;gap:8px;align-items:center;">
            <input type="hidden" name="status" value="{{ $status }}">
            <div class="search-bar" style="width:260px;">
                <i class="fas fa-search search-bar-icon"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari pelapor, UMKM, produk...">
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
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Pelapor</th>
                        <th>Target Laporan</th>
                        <th>Kategori</th>
                        <th>Deskripsi</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reports as $report)
                        <tr>
                            <td><span style="font-family:monospace;color:#9ca3af;">#{{ $report->id }}</span></td>

                            <td>
                                <div style="display:flex;align-items:center;gap:8px;">
                                    <div style="width:32px;height:32px;border-radius:50%;background:#fef3c7;color:#b45309;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;flex-shrink:0;">
                                        {{ strtoupper(substr($report->reporter->name ?? '?', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div style="font-weight:600;font-size:13px;color:#111827;">{{ $report->reporter->name ?? '-' }}</div>
                                        <div style="font-size:11px;color:#9ca3af;">{{ $report->reporter->email ?? '' }}</div>
                                    </div>
                                </div>
                            </td>

                            <td>
                                @if($report->product)
                                    <div>
                                        <div style="font-size:10px;font-weight:700;text-transform:uppercase;color:#d97706;margin-bottom:2px;">Produk</div>
                                        <div style="font-weight:600;font-size:13px;color:#111827;">{{ Str::limit($report->product->name, 28) }}</div>
                                        <div style="font-size:11px;color:#9ca3af;">{{ $report->umkm->name ?? '' }}</div>
                                    </div>
                                @elseif($report->umkm)
                                    <div>
                                        <div style="font-size:10px;font-weight:700;text-transform:uppercase;color:#4f46e5;margin-bottom:2px;">UMKM</div>
                                        <div style="font-weight:600;font-size:13px;color:#111827;">{{ $report->umkm->name }}</div>
                                    </div>
                                @else
                                    <span style="font-size:12px;color:#d1d5db;">Target dihapus</span>
                                @endif
                            </td>

                            <td>
                                <span class="badge" style="background:#f3f4f6;color:#374151;white-space:nowrap;">
                                    {{ \App\Models\Report::$categories[$report->category] ?? $report->category }}
                                </span>
                            </td>

                            <td style="max-width:220px;">
                                <p style="font-size:13px;color:#4b5563;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;" title="{{ $report->description }}">
                                    {{ $report->description }}
                                </p>
                            </td>

                            <td>
                                @if($report->status === 'PENDING')
                                    <span class="badge badge-pending">Pending</span>
                                @elseif($report->status === 'REVIEWED')
                                    <span class="badge badge-approved">Reviewed</span>
                                @else
                                    <span class="badge" style="background:#f3f4f6;color:#6b7280;">Dismissed</span>
                                @endif
                            </td>

                            <td>
                                <div style="font-size:13px;color:#111827;">{{ $report->created_at->format('d M Y') }}</div>
                                <div style="font-size:11px;color:#9ca3af;">{{ $report->created_at->format('H:i') }}</div>
                            </td>

                            <td>
                                <a href="{{ route('reports.show', $report) }}" class="btn btn-xs btn-secondary">
                                    <i class="fas fa-eye"></i> Detail
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
