@extends('layouts.app')

@section('title', 'UMKM: ' . $umkm->name)
@section('page-title', 'Detail UMKM')
@section('page-subtitle', $umkm->name)

@section('topbar-actions')
    <a href="{{ route('umkms.index') }}" class="btn btn-secondary btn-sm">← Kembali</a>
@endsection

@section('content')

    <style>
        .umkm-detail-container {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }

        @media (min-width: 768px) {
            .umkm-detail-container {
                grid-template-columns: 320px 1fr;
            }
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid var(--dark-700);
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .info-list li {
            margin: 0;
        }

        .info-section {
            margin: 16px 0;
            padding: 16px;
            background: var(--dark-800);
            border-radius: 6px;
        }

        .info-section:first-of-type {
            margin-top: 0;
        }

        .info-section-title {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-section {
            margin-bottom: 20px;
            padding: 16px;
            background: var(--dark-800);
            border-radius: 6px;
        }

        .form-section-title {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr;
            gap: 12px;
        }

        @media (min-width: 768px) {
            .form-row {
                grid-template-columns: 1fr 1fr;
            }
        }

        .form-group {
            margin-bottom: 0;
        }

        .form-group:last-child {
            margin-bottom: 0;
        }

        .btn-submit-block {
            width: 100%;
            justify-content: center;
            margin-top: 16px;
        }

        .btn-wrapper {
            padding: 16px;
            margin-top: 12px;
            background: var(--dark-800);
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-wrapper form {
            width: 100%;
        }

        .btn-wrapper button {
            width: 100%;
        }
    </style>

    <div class="umkm-detail-container">

        <div>
            <div class="card mb-4">
                <div class="card-body">
                    <div style="text-align:center;margin-bottom:16px;">
                        <div style="font-size:48px;margin-bottom:12px;">🏪</div>
                        <h2 style="font-size:18px;font-weight:700;color:var(--text-primary);margin-bottom:4px;">
                            {{ $umkm->name }}</h2>
                        <p style="font-size:13px;color:var(--text-muted);">{{ $umkm->description ?? 'Tidak ada deskripsi.' }}
                        </p>
                    </div>
                    <div class="divider"></div>
                    <div class="info-section">
                        <div class="info-section-title">ℹ️ Informasi Dasar</div>
                        <ul class="info-list">
                            <li class="info-row">
                                <span class="td-muted">Owner</span>
                                <span class="td-primary">{{ $umkm->owner->name }}</span>
                            </li>
                            <li class="info-row">
                                <span class="td-muted">Status Verifikasi</span>
                                <span class="td-primary">
                                    @if ($umkm->is_verified)
                                        <span class="badge badge-success">Terverifikasi</span>
                                    @else
                                        <span class="badge badge-warning">Belum Diverifikasi</span>
                                    @endif
                                </span>
                            </li>
                        </ul>
                    </div>
                    @if (auth()->user()->isSuperAdmin())
                        <div class="btn-wrapper">
                            <form method="POST" action="{{ route('umkms.toggleVerify', $umkm) }}">
                                @csrf
                                @method('PATCH')
                                <button class="btn btn-xs {{ $umkm->is_verified ? 'btn-warning' : 'btn-success' }}">
                                    {{ $umkm->is_verified ? '✗ Batalkan Verifikasi' : '✓ Verifikasi' }}
                                </button>
                            </form>
                        </div>
                    @endif

                    <div class="info-section">
                        <div class="info-section-title">💰 Komisi Platform</div>
                        <ul class="info-list">
                            <li class="info-row">
                                <span class="td-muted">Tipe</span>
                                <span class="td-primary">{{ ucfirst($umkm->platform_fee_type) }}</span>
                            </li>
                            @if ($umkm->platform_fee_type === 'percentage')
                                <li class="info-row">
                                    <span class="td-muted">Rate</span>
                                    <span
                                        style="color:var(--coffee-400);font-weight:700;">{{ $umkm->platform_fee_rate }}%</span>
                                </li>
                            @else
                                <li class="info-row">
                                    <span class="td-muted">Flat</span>
                                    <span style="color:var(--coffee-400);font-weight:700;">Rp
                                        {{ number_format($umkm->platform_fee_flat, 0, ',', '.') }}</span>
                                </li>
                            @endif
                            <li class="info-row">
                                <span class="td-muted">Bergabung</span>
                                <span class="td-primary">{{ $umkm->created_at->format('d M Y') }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><span class="card-title">⚡ Atur Fee & Komisi</span></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('umkms.updatePlatformFee', $umkm) }}">
                        @csrf @method('PATCH')

                        {{-- Tipe & Nilai Platform Fee --}}
                        <div class="form-section">
                            <div class="form-section-title">📋 Komisi Platform (per Transaksi)</div>
                            <p style="font-size:12px;color:#9ca3af;margin-bottom:12px;">
                                Komisi ini merupakan "pajak" yang dikenakan platform kepada UMKM.
                                Berlaku hanya setelah total keuntungan kumulatif melampaui batas threshold di bawah.
                            </p>
                            <div class="form-group" style="margin-bottom:12px;">
                                <label class="form-label">Tipe Komisi</label>
                                <select name="platform_fee_type" class="form-control" id="fee-type-select">
                                    <option value="percentage" {{ $umkm->platform_fee_type === 'percentage' ? 'selected' : '' }}>Persentase (%) dari tiap transaksi</option>
                                    <option value="flat"       {{ $umkm->platform_fee_type === 'flat'       ? 'selected' : '' }}>Flat (Rp) per transaksi</option>
                                </select>
                            </div>
                            <div class="form-row">
                                <div class="form-group" id="fee-rate-group">
                                    <label class="form-label">Rate Persentase (%)</label>
                                    <input type="number" name="platform_fee_rate" class="form-control"
                                        value="{{ $umkm->platform_fee_rate }}" min="0" max="100" step="0.5"
                                        placeholder="Contoh: 5" />
                                </div>
                                <div class="form-group" id="fee-flat-group" style="display:none;">
                                    <label class="form-label">Nilai Flat (Rp)</label>
                                    <input type="number" name="platform_fee_flat" class="form-control"
                                        value="{{ $umkm->platform_fee_flat }}" min="0" step="500"
                                        placeholder="Contoh: 2000" />
                                </div>
                            </div>
                        </div>

                        {{-- Threshold --}}
                        <div class="form-section" style="margin-top:12px;">
                            <div class="form-section-title">📊 Batas Keuntungan (Threshold)</div>
                            <p style="font-size:12px;color:#9ca3af;margin-bottom:12px;">
                                Komisi platform di atas <strong>baru berlaku</strong> setelah total keuntungan
                                kumulatif UMKM melampaui angka ini. Di bawah threshold → komisi = Rp 0.
                                Isi <strong>0</strong> agar komisi tidak pernah berlaku.
                            </p>
                            <div class="form-group">
                                <label class="form-label">Batas Threshold Keuntungan Kumulatif (Rp)</label>
                                <input type="number" name="tax_threshold" class="form-control"
                                    value="{{ $umkm->tax_threshold }}" min="0" step="100000"
                                    placeholder="Contoh: 10000000 (Rp 10 juta)" />
                                <small style="color:#9ca3af;font-size:11px;">0 = komisi tidak pernah berlaku</small>
                            </div>
                        </div>

                        {{-- Info Biaya DOKU --}}
                        <div style="margin-top:12px;padding:12px;background:#f0fdf4;border-radius:8px;border:1px solid #bbf7d0;font-size:12px;">
                            <p style="font-weight:700;color:#15803d;margin-bottom:6px;"><i class="fas fa-building-columns"></i> Biaya Transfer DOKU (Global)</p>
                            <p style="color:#166534;">
                                Biaya admin penarikan mengikuti tarif transfer DOKU sebesar
                                <strong>Rp {{ number_format((float) config('doku.withdrawal_fee', 6500), 0, ',', '.') }}</strong> per penarikan.
                                Biaya ini ditanggung UMKM dan dipotong otomatis saat penarikan diproses.
                            </p>
                        </div>

                        {{-- Preview Aktif --}}
                        <div style="margin-top:12px;padding:12px;background:#f9fafb;border-radius:8px;border:1px solid #f3f4f6;font-size:12px;">
                            <p style="font-weight:700;color:#374151;margin-bottom:8px;">📝 Konfigurasi Aktif Saat Ini</p>
                            <div style="display:flex;flex-direction:column;gap:4px;color:#6b7280;">
                                <span>• Komisi: <strong style="color:#111827;">
                                    @if($umkm->platform_fee_type === 'percentage')
                                        {{ $umkm->platform_fee_rate }}% dari tiap transaksi
                                    @else
                                        Rp {{ number_format($umkm->platform_fee_flat, 0, ',', '.') }} per transaksi
                                    @endif
                                </strong></span>
                                <span>• Berlaku setelah kumulatif melampaui: <strong style="color:#111827;">
                                    @if($umkm->tax_threshold > 0)
                                        Rp {{ number_format($umkm->tax_threshold, 0, ',', '.') }}
                                    @else
                                        <span style="color:#9ca3af;">Tidak dikonfigurasi (komisi tidak berlaku)</span>
                                    @endif
                                </strong></span>
                                <span>• Biaya Transfer DOKU: <strong style="color:#111827;">Rp {{ number_format((float) config('doku.withdrawal_fee', 6500), 0, ',', '.') }}</strong> (global)</span>
                            </div>
                        </div>

                        <div class="btn-wrapper">
                            <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
                        </div>
                    </form>
                </div>
            </div>

            @if (auth()->user()->isSuperAdmin())
                <div class="card">
                    <div class="card-header"><span class="card-title">⚠️ Zona Berbahaya</span></div>
                    <div class="card-body">
                        <div class="btn-wrapper">
                            <form method="POST" action="{{ route('umkms.destroy', $umkm) }}"
                                onsubmit="return confirm('PERINGATAN: Menghapus UMKM akan menghapus semua produknya juga. Lanjutkan?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger">🗑 Hapus UMKM Ini</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div>
            {{-- Stats --}}
            <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:12px;margin-bottom:20px;">
                @foreach ([['icon' => '📦', 'value' => $stats['total_products'], 'label' => 'Total Produk'], ['icon' => '⏳', 'value' => $stats['pending_products'], 'label' => 'Pending', 'color' => '#fbbf24'], ['icon' => '✅', 'value' => $stats['approved_products'], 'label' => 'Approved', 'color' => '#4ade80'], ['icon' => '🛒', 'value' => $stats['total_orders'], 'label' => 'Total Order'], ['icon' => '💰', 'value' => 'Rp ' . number_format($stats['total_revenue'], 0, ',', '.'), 'label' => 'Revenue', 'small' => true]] as $s)
                    <div class="stat-card" style="flex-direction:column;text-align:center;gap:6px;">
                        <div style="font-size:24px;">{{ $s['icon'] }}</div>
                        <div
                            style="font-size:{{ $s['small'] ?? false ? '13' : '22' }}px;font-weight:800;color:{{ $s['color'] ?? 'var(--text-primary)' }};">
                            {{ $s['value'] }}</div>
                        <div style="font-size:11px;color:var(--text-muted);">{{ $s['label'] }}</div>
                    </div>
                @endforeach
            </div>

            {{-- Products --}}
            <div class="card">
                <div class="card-header">
                    <span class="card-title">📦 Daftar Produk</span>
                    <a href="{{ route('products.index') }}?search={{ $umkm->name }}"
                        class="btn btn-sm btn-ghost">Lihat
                        Semua →</a>
                </div>
                <div class="table-wrap">
                    @if ($umkm->products->isEmpty())
                        <div class="empty-state" style="padding:30px;">
                            <div>📦</div>
                            <p>Belum ada produk.</p>
                        </div>
                    @else
                        <table>
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>Harga</th>
                                    <th>Stok</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($umkm->products->take(10) as $product)
                                    <tr>
                                        <td class="td-primary">{{ $product->name }}</td>
                                        <td style="color:var(--coffee-400);font-weight:600;">Rp
                                            {{ number_format($product->price, 0, ',', '.') }}</td>
                                        <td class="td-primary">{{ $product->stock }}</td>
                                        <td><span
                                                class="badge badge-{{ strtolower($product->status) }}">{{ $product->status }}</span>
                                        </td>
                                        <td>
                                            <div style="display:flex;gap:4px;">
                                                @if ($product->status === 'PENDING')
                                                    <form method="POST"
                                                        action="{{ route('products.approve', $product) }}">
                                                        @csrf @method('PATCH')
                                                        <button class="btn btn-xs btn-success">✓</button>
                                                    </form>
                                                    <form method="POST"
                                                        action="{{ route('products.reject', $product) }}">
                                                        @csrf @method('PATCH')
                                                        <button class="btn btn-xs btn-danger">✗</button>
                                                    </form>
                                                @endif
                                                <a href="{{ route('products.show', $product) }}"
                                                    class="btn btn-xs btn-secondary">Detail</a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection
