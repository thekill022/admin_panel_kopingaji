@extends('layouts.app')

@section('title', 'Detail Laporan #' . $report->id)
@section('page-title', 'Detail Laporan')

@section('content')
<div class="space-y-6">
    
    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('reports.index') }}" class="hover:text-amber-600 transition-colors">Laporan</a>
        <i class="fas fa-chevron-right text-xs"></i>
        <span class="text-gray-900 font-medium">Laporan #{{ $report->id }}</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Detail Card -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Report Content -->
            <div class="card">
                <div class="card-header">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-red-100 text-red-600 rounded-xl flex items-center justify-center">
                            <i class="fas fa-flag"></i>
                        </div>
                        <div>
                            <p class="card-title">Konten Laporan</p>
                            <p class="text-xs text-gray-400">Diajukan {{ $report->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    <div>
                        @if($report->status === 'PENDING')
                            <span class="badge badge-pending">Pending</span>
                        @elseif($report->status === 'REVIEWED')
                            <span class="badge badge-approved">Reviewed</span>
                        @else
                            <span class="badge" style="background:#f3f4f6; color:#6b7280;">Dismissed</span>
                        @endif
                    </div>
                </div>
                <div class="p-6 space-y-4">
                    <!-- Category -->
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Kategori</p>
                        <span class="badge" style="background:#fef3c7; color:#b45309; font-size:0.8rem; padding: 0.35rem 0.85rem;">
                            {{ \App\Models\Report::$categories[$report->category] ?? $report->category }}
                        </span>
                    </div>
                    <!-- Description -->
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Deskripsi Laporan</p>
                        <p class="text-sm text-gray-700 bg-gray-50 rounded-xl p-4 leading-relaxed border border-gray-100">{{ $report->description }}</p>
                    </div>
                    @if($report->admin_note)
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Catatan Admin</p>
                            <p class="text-sm text-gray-700 bg-blue-50 rounded-xl p-4 leading-relaxed border border-blue-100">{{ $report->admin_note }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Target Info -->
            <div class="card">
                <div class="card-header"><p class="card-title">Target yang Dilaporkan</p></div>
                <div class="p-6">
                    @if($report->product)
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-xl bg-slate-100 flex items-center justify-center shrink-0">
                                @if($report->product->image_url)
                                    <img src="{{ asset('storage/' . $report->product->image_url) }}" class="w-full h-full object-cover rounded-xl" alt="">
                                @else
                                    <i class="fas fa-image text-slate-300 text-2xl"></i>
                                @endif
                            </div>
                            <div>
                                <p class="text-xs font-bold text-amber-600 uppercase">Produk</p>
                                <p class="font-bold text-gray-900">{{ $report->product->name }}</p>
                                <p class="text-sm text-gray-500">{{ $report->product->umkm->name ?? '-' }}</p>
                                <p class="text-sm font-semibold text-indigo-600 mt-1">Rp {{ number_format($report->product->price, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    @elseif($report->umkm)
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-xl bg-indigo-50 text-indigo-400 flex items-center justify-center shrink-0 text-2xl">
                                <i class="fas fa-store"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-indigo-600 uppercase">UMKM</p>
                                <p class="font-bold text-gray-900">{{ $report->umkm->name }}</p>
                                <p class="text-sm text-gray-500">{{ $report->umkm->description ?? 'Tidak ada deskripsi' }}</p>
                            </div>
                        </div>
                    @else
                        <p class="text-gray-400 text-sm">Target laporan telah dihapus.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Panel -->
        <div class="space-y-6">
            <!-- Reporter Info -->
            <div class="card">
                <div class="card-header"><p class="card-title">Pelapor</p></div>
                <div class="p-6 flex items-center gap-4">
                    <div class="w-12 h-12 bg-amber-100 text-amber-700 rounded-full flex items-center justify-center font-bold text-lg shrink-0">
                        {{ strtoupper(substr($report->reporter->name ?? '?', 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-bold text-gray-900">{{ $report->reporter->name ?? '-' }}</p>
                        <p class="text-sm text-gray-500">{{ $report->reporter->email ?? '' }}</p>
                        <p class="text-xs text-gray-400 mt-1">Bergabung {{ $report->reporter->created_at->format('d M Y') ?? '' }}</p>
                    </div>
                </div>
            </div>

            <!-- Admin Actions -->
            @if($report->status === 'PENDING')
                <div class="card">
                    <div class="card-header"><p class="card-title">Tindak Lanjut</p></div>
                    <div class="p-6 space-y-4">
                        <!-- Mark Reviewed -->
                        <form method="POST" action="{{ route('reports.review', $report) }}">
                            @csrf @method('PATCH')
                            <div class="mb-3">
                                <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Catatan (opsional)</label>
                                <textarea name="admin_note" rows="3" placeholder="Tambahkan catatan tindakan..."
                                    class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-200 focus:border-green-400 resize-none outline-none transition-colors"></textarea>
                            </div>
                            <button type="submit" class="btn btn-success w-full justify-center" onclick="return confirm('Tandai laporan ini sebagai sudah ditinjau?')">
                                <i class="fas fa-check"></i> Tandai Reviewed
                            </button>
                        </form>

                        <hr class="border-gray-100">

                        <!-- Dismiss -->
                        <form method="POST" action="{{ route('reports.dismiss', $report) }}">
                            @csrf @method('PATCH')
                            <div class="mb-3">
                                <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Alasan Dismiss</label>
                                <textarea name="admin_note" rows="2" placeholder="Alasan laporan ditolak..."
                                    class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-200 focus:border-red-400 resize-none outline-none transition-colors"></textarea>
                            </div>
                            <button type="submit" class="btn btn-danger w-full justify-center" onclick="return confirm('Dismiss laporan ini?')">
                                <i class="fas fa-times"></i> Dismiss Laporan
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div class="card p-6">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Laporan Telah Ditindaklanjuti</p>
                    <p class="text-sm text-gray-600">Status: 
                        <span class="font-bold {{ $report->status === 'REVIEWED' ? 'text-green-600' : 'text-gray-500' }}">
                            {{ $report->status }}
                        </span>
                    </p>
                    @if($report->admin_note)
                        <p class="text-sm text-gray-500 mt-2">{{ $report->admin_note }}</p>
                    @endif
                </div>
            @endif

            <!-- Delete -->
            <form method="POST" action="{{ route('reports.destroy', $report) }}" onsubmit="return confirm('Hapus laporan ini secara permanen?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm w-full justify-center">
                    <i class="fas fa-trash"></i> Hapus Laporan
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
