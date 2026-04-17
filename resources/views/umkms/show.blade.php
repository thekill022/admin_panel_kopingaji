@extends('layouts.app')

@section('title', 'UMKM: ' . $umkm->name)
@section('page-title', 'Detail UMKM')
@section('page-subtitle', $umkm->name)

@section('topbar-actions')
    <a href="{{ route('umkms.index') }}" class="btn btn-secondary btn-sm">← Kembali</a>
@endsection

@section('content')

    <style>
        .umkm-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 24px;
        }
        @media (min-width: 1024px) {
            .umkm-grid { grid-template-columns: 360px 1fr; }
        }

        /* ── Profile Banner ── */
        .umkm-banner {
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 50%, #fde68a 100%);
            border: 1px solid #fde68a;
            border-radius: 1rem;
            padding: 2rem;
            text-align: center;
        }
        .umkm-banner::before {
            content: '';
            position: absolute;
            right: -30px;
            top: -30px;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: rgba(196, 129, 31, 0.08);
        }
        .umkm-banner-icon {
            width: 72px;
            height: 72px;
            margin: 0 auto 12px;
            background: linear-gradient(135deg, #c4811f, #a86618);
            border-radius: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
            box-shadow: 0 8px 24px rgba(196, 129, 31, 0.25);
        }
        .umkm-banner-name {
            font-size: 1.25rem;
            font-weight: 800;
            color: #111827;
            margin-bottom: 4px;
        }
        .umkm-banner-desc {
            font-size: 0.8rem;
            color: #92400e;
            line-height: 1.4;
        }

        /* ── Meta List ── */
        .u-meta-list { list-style: none; padding: 0; margin: 0; }
        .u-meta-list li {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.7rem 0;
            border-bottom: 1px solid #f3f4f6;
            font-size: 0.8rem;
        }
        .u-meta-list li:last-child { border-bottom: 0; }
        .u-meta-label { color: #9ca3af; font-weight: 500; }
        .u-meta-value { color: #111827; font-weight: 700; }

        /* ── Section Inside Card ── */
        .u-section {
            padding: 1rem;
            background: #f9fafb;
            border-radius: 0.75rem;
            border: 1px solid #f3f4f6;
            margin-bottom: 1rem;
        }
        .u-section:last-child { margin-bottom: 0; }
        .u-section-title {
            font-size: 0.7rem;
            font-weight: 700;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 10px;
        }

        /* ── Form Styling ── */
        .u-form-label {
            display: block;
            font-size: 0.7rem;
            font-weight: 700;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 6px;
        }
        .u-form-control {
            width: 100%;
            padding: 0.6rem 0.85rem;
            border-radius: 0.75rem;
            border: 1px solid #e5e7eb;
            font-size: 0.85rem;
            font-weight: 500;
            color: #374151;
            background: white;
            transition: border-color 0.2s;
        }
        .u-form-control:focus { border-color: #c4811f; outline: none; box-shadow: 0 0 0 3px rgba(196,129,31,0.1); }
        .u-form-hint {
            font-size: 0.65rem;
            color: #9ca3af;
            margin-top: 4px;
        }
        .u-form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        /* ── Stat Cards ── */
        .u-stats-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 12px;
        }
        .u-stat-card {
            background: white;
            border: 1px solid #f3f4f6;
            border-radius: 1rem;
            padding: 1rem 0.5rem;
            text-align: center;
            transition: all 0.2s;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .u-stat-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.08); transform: translateY(-2px); }
        .u-stat-icon { font-size: 1.5rem; margin-bottom: 6px; }
        .u-stat-value { font-size: 1.25rem; font-weight: 800; color: #111827; }
        .u-stat-value.small { font-size: 0.85rem; }
        .u-stat-label { font-size: 0.6rem; font-weight: 700; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.03em; margin-top: 2px; }

        /* ── Product Table ── */
        .u-prod-table { table-layout: fixed; width: 100%; }
        .u-prod-table th, .u-prod-table td { padding: 0.7rem 0.75rem; font-size: 0.8rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; vertical-align: middle; }
        .u-prod-table th { font-size: 0.65rem; padding: 0.55rem 0.75rem; }
        .u-prod-table .col-name   { width: 35%; }
        .u-prod-table .col-price  { width: 18%; }
        .u-prod-table .col-stock  { width: 10%; }
        .u-prod-table .col-status { width: 15%; }
        .u-prod-table .col-action { width: 22%; }
        .u-prod-action { display: flex; gap: 4px; }
        .u-prod-action .btn { font-size: 0.6rem; padding: 0.2rem 0.45rem; }

        /* ── Alert Box ── */
        .u-alert-danger {
            padding: 1rem;
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 0.75rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
        }
        .u-alert-danger-icon {
            width: 32px; height: 32px;
            border-radius: 50%;
            background: #fee2e2;
            color: #dc2626;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            font-size: 0.85rem;
        }
        .u-alert-danger-title { font-size: 0.8rem; font-weight: 700; color: #991b1b; margin-bottom: 2px; }
        .u-alert-danger-text { font-size: 0.72rem; color: #b91c1c; line-height: 1.4; }

        /* ── Info Callout ── */
        .u-callout {
            padding: 0.85rem 1rem;
            border-radius: 0.75rem;
            font-size: 0.75rem;
            line-height: 1.5;
            margin-top: 12px;
        }
        .u-callout-green { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }
        .u-callout-gray { background: #f9fafb; border: 1px solid #f3f4f6; color: #6b7280; }
        .u-callout strong { color: #111827; }
        .u-callout-title { font-weight: 700; margin-bottom: 4px; }
    </style>

    {{-- ══════ MAIN GRID ══════ --}}
    <div class="umkm-grid">

        {{-- ════ LEFT COLUMN ════ --}}
        <div>
            {{-- Profile Banner --}}
            <div class="umkm-banner" style="margin-bottom: 20px;">
                <div class="umkm-banner-icon"><i class="fas fa-store"></i></div>
                <div class="umkm-banner-name">{{ $umkm->name }}</div>
                <div class="umkm-banner-desc">{{ $umkm->description ?? 'Tidak ada deskripsi.' }}</div>
            </div>

            {{-- Info Card --}}
            <div class="card" style="margin-bottom: 20px;">
                <div class="card-header">
                    <span class="card-title"><i class="fas fa-info-circle" style="color:#c4811f;margin-right:6px;"></i> Informasi</span>
                </div>
                <div style="padding: 1.25rem 1.5rem;">
                    <ul class="u-meta-list">
                        <li>
                            <span class="u-meta-label"><i class="fas fa-user-tie" style="margin-right:6px;font-size:0.7rem;"></i> Owner</span>
                            <span class="u-meta-value">{{ $umkm->owner->name }}</span>
                        </li>
                        <li>
                            <span class="u-meta-label"><i class="fas fa-shield-alt" style="margin-right:6px;font-size:0.7rem;"></i> Verifikasi</span>
                            <span>
                                @if ($umkm->is_verified)
                                    <span class="badge badge-approved" style="font-size:0.6rem;padding:0.2rem 0.5rem;"><i class="fas fa-check" style="margin-right:2px;font-size:0.5rem;"></i> Terverifikasi</span>
                                @else
                                    <span class="badge badge-pending" style="font-size:0.6rem;padding:0.2rem 0.5rem;">Belum Diverifikasi</span>
                                @endif
                            </span>
                        </li>
                        <li>
                            <span class="u-meta-label"><i class="fas fa-coins" style="margin-right:6px;font-size:0.7rem;"></i> Fee</span>
                            <span class="u-meta-value" style="color:#c4811f;">
                                @if ($umkm->platform_fee_type === 'percentage')
                                    {{ $umkm->platform_fee_rate }}%
                                @else
                                    Rp {{ number_format($umkm->platform_fee_flat, 0, ',', '.') }}
                                @endif
                                <span style="font-size:0.6rem;color:#9ca3af;font-weight:400;">({{ ucfirst($umkm->platform_fee_type) }})</span>
                            </span>
                        </li>
                        <li>
                            <span class="u-meta-label"><i class="far fa-calendar" style="margin-right:6px;font-size:0.7rem;"></i> Bergabung</span>
                            <span class="u-meta-value">{{ $umkm->created_at->format('d M Y') }}</span>
                        </li>
                    </ul>

                    {{-- Owner not verified warning --}}
                    @if (! $umkm->owner->is_verified)
                        <div class="u-alert-danger" style="margin-top: 1rem;">
                            <div class="u-alert-danger-icon"><i class="fas fa-exclamation-triangle"></i></div>
                            <div>
                                <div class="u-alert-danger-title">Pemilik Belum Diverifikasi</div>
                                <div class="u-alert-danger-text">
                                    UMKM tidak dapat diverifikasi karena <strong>{{ $umkm->owner->name }}</strong> belum diverifikasi.
                                    Verifikasi pengguna terlebih dahulu.
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Verify Toggle --}}
                    @if (auth()->user()->isSuperAdmin())
                        <div style="margin-top: 1rem;">
                            <form method="POST" action="{{ route('umkms.toggleVerify', $umkm) }}">
                                @csrf @method('PATCH')
                                <button class="btn {{ $umkm->is_verified ? 'btn-warning' : 'btn-success' }}" style="width:100%;justify-content:center;"
                                    {{ ! $umkm->owner->is_verified && ! $umkm->is_verified ? 'disabled' : '' }}>
                                    <i class="fas {{ $umkm->is_verified ? 'fa-times-circle' : 'fa-check-circle' }}"></i>
                                    {{ $umkm->is_verified ? 'Batalkan Verifikasi' : 'Verifikasi UMKM' }}
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Fee & Commission Settings --}}
            <div class="card" style="margin-bottom: 20px;">
                <div class="card-header">
                    <span class="card-title"><i class="fas fa-sliders-h" style="color:#c4811f;margin-right:6px;"></i> Atur Fee & Komisi</span>
                </div>
                <div style="padding: 1.25rem 1.5rem;">
                    <form method="POST" action="{{ route('umkms.updatePlatformFee', $umkm) }}">
                        @csrf @method('PATCH')

                        {{-- Tipe & Nilai --}}
                        <div class="u-section">
                            <div class="u-section-title"><i class="fas fa-percentage" style="margin-right:4px;"></i> Komisi Platform (per Transaksi)</div>
                            <p style="font-size:0.72rem;color:#9ca3af;margin-bottom:12px;line-height:1.4;">
                                Komisi dikenakan setelah total keuntungan kumulatif melampaui threshold.
                            </p>
                            <div style="margin-bottom:12px;">
                                <label class="u-form-label">Tipe Komisi</label>
                                <select name="platform_fee_type" class="u-form-control" id="fee-type-select">
                                    <option value="percentage" {{ $umkm->platform_fee_type === 'percentage' ? 'selected' : '' }}>Persentase (%) per transaksi</option>
                                    <option value="flat" {{ $umkm->platform_fee_type === 'flat' ? 'selected' : '' }}>Flat (Rp) per transaksi</option>
                                </select>
                            </div>
                            <div class="u-form-row">
                                <div id="fee-rate-group">
                                    <label class="u-form-label">Rate (%)</label>
                                    <input type="number" name="platform_fee_rate" class="u-form-control"
                                        value="{{ $umkm->platform_fee_rate }}" min="0" max="100" step="0.5" placeholder="5" />
                                </div>
                                <div id="fee-flat-group" style="display:none;">
                                    <label class="u-form-label">Flat (Rp)</label>
                                    <input type="number" name="platform_fee_flat" class="u-form-control"
                                        value="{{ $umkm->platform_fee_flat }}" min="0" step="500" placeholder="2000" />
                                </div>
                            </div>
                        </div>

                        {{-- Threshold --}}
                        <div class="u-section">
                            <div class="u-section-title"><i class="fas fa-chart-line" style="margin-right:4px;"></i> Batas Threshold</div>
                            <p style="font-size:0.72rem;color:#9ca3af;margin-bottom:12px;line-height:1.4;">
                                Komisi <strong>baru berlaku</strong> setelah kumulatif melampaui angka ini. Isi <strong>0</strong> = komisi tidak berlaku.
                            </p>
                            <label class="u-form-label">Threshold (Rp)</label>
                            <input type="number" name="tax_threshold" class="u-form-control"
                                value="{{ $umkm->tax_threshold }}" min="0" step="100000" placeholder="10000000" />
                            <div class="u-form-hint">0 = komisi tidak pernah berlaku</div>
                        </div>

                        {{-- DOKU Info --}}
                        <div class="u-callout u-callout-green">
                            <div class="u-callout-title"><i class="fas fa-building-columns" style="margin-right:4px;"></i> Biaya Transfer DOKU (Global)</div>
                            Biaya admin penarikan: <strong>Rp {{ number_format((float) config('doku.withdrawal_fee', 6500), 0, ',', '.') }}</strong> per penarikan. Ditanggung UMKM, dipotong otomatis.
                        </div>

                        {{-- Preview --}}
                        <div class="u-callout u-callout-gray">
                            <div class="u-callout-title">📝 Konfigurasi Aktif</div>
                            <div style="display:flex;flex-direction:column;gap:3px;">
                                <span>• Komisi: <strong>
                                    @if($umkm->platform_fee_type === 'percentage')
                                        {{ $umkm->platform_fee_rate }}% per transaksi
                                    @else
                                        Rp {{ number_format($umkm->platform_fee_flat, 0, ',', '.') }} per transaksi
                                    @endif
                                </strong></span>
                                <span>• Threshold: <strong>
                                    @if($umkm->tax_threshold > 0)
                                        Rp {{ number_format($umkm->tax_threshold, 0, ',', '.') }}
                                    @else
                                        <span style="color:#9ca3af;">Tidak aktif</span>
                                    @endif
                                </strong></span>
                                <span>• DOKU: <strong>Rp {{ number_format((float) config('doku.withdrawal_fee', 6500), 0, ',', '.') }}</strong> (global)</span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:16px;">
                            <i class="fas fa-save"></i> Simpan Pengaturan
                        </button>
                    </form>
                </div>
            </div>

            {{-- Danger Zone --}}
            @if (auth()->user()->isSuperAdmin())
                <div class="card">
                    <div class="card-header" style="background:#fef2f2;border-color:#fecaca;">
                        <span class="card-title" style="color:#dc2626;"><i class="fas fa-exclamation-triangle" style="margin-right:6px;"></i> Zona Berbahaya</span>
                    </div>
                    <div style="padding: 1.25rem 1.5rem;">
                        <p style="font-size:0.75rem;color:#6b7280;margin-bottom:12px;">Menghapus UMKM akan menghapus semua produknya juga. Tindakan ini tidak dapat dibatalkan.</p>
                        <form method="POST" action="{{ route('umkms.destroy', $umkm) }}"
                            onsubmit="return confirm('PERINGATAN: Menghapus UMKM akan menghapus semua produknya juga. Lanjutkan?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger" style="width:100%;justify-content:center;">
                                <i class="fas fa-trash-alt"></i> Hapus UMKM Ini
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>

        {{-- ════ RIGHT COLUMN ════ --}}
        <div>
            {{-- Stats --}}
            <div class="u-stats-grid" style="margin-bottom: 24px;">
                @foreach ([
                    ['icon' => '📦', 'value' => $stats['total_products'], 'label' => 'Produk'],
                    ['icon' => '⏳', 'value' => $stats['pending_products'], 'label' => 'Pending', 'color' => '#d97706'],
                    ['icon' => '✅', 'value' => $stats['approved_products'], 'label' => 'Approved', 'color' => '#16a34a'],
                    ['icon' => '🛒', 'value' => $stats['total_orders'], 'label' => 'Order'],
                    ['icon' => '💰', 'value' => 'Rp ' . number_format($stats['total_revenue'], 0, ',', '.'), 'label' => 'Revenue', 'small' => true, 'color' => '#c4811f'],
                ] as $s)
                    <div class="u-stat-card">
                        <div class="u-stat-icon">{{ $s['icon'] }}</div>
                        <div class="u-stat-value {{ ($s['small'] ?? false) ? 'small' : '' }}" style="color: {{ $s['color'] ?? '#111827' }};">{{ $s['value'] }}</div>
                        <div class="u-stat-label">{{ $s['label'] }}</div>
                    </div>
                @endforeach
            </div>

            {{-- Products Table --}}
            <div class="card">
                <div class="card-header">
                    <span class="card-title"><i class="fas fa-box" style="color:#c4811f;margin-right:6px;"></i> Daftar Produk</span>
                    <a href="{{ route('products.index') }}?search={{ $umkm->name }}" class="btn btn-xs btn-secondary" style="font-size:0.65rem;">Lihat Semua →</a>
                </div>
                <div style="overflow-x:hidden;">
                    @if ($umkm->products->isEmpty())
                        <div style="padding:3rem 2rem;text-align:center;">
                            <div style="font-size:3rem;opacity:0.15;margin-bottom:0.75rem;"><i class="fas fa-box-open"></i></div>
                            <p style="color:#9ca3af;font-size:0.8rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Belum ada produk</p>
                        </div>
                    @else
                        <table class="u-prod-table">
                            <thead>
                                <tr>
                                    <th class="col-name">Produk</th>
                                    <th class="col-price">Harga</th>
                                    <th class="col-stock">Stok</th>
                                    <th class="col-status">Status</th>
                                    <th class="col-action">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($umkm->products->take(10) as $product)
                                    <tr>
                                        <td class="col-name" style="font-weight:700;color:#111827;">{{ $product->name }}</td>
                                        <td class="col-price" style="color:#c4811f;font-weight:600;font-size:0.78rem;">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                                        <td class="col-stock" style="font-weight:600;color:#374151;">{{ $product->stock }}</td>
                                        <td class="col-status"><span class="badge badge-{{ strtolower($product->status) }}" style="font-size:0.6rem;padding:0.2rem 0.5rem;">{{ $product->status }}</span></td>
                                        <td class="col-action">
                                            <div class="u-prod-action">
                                                @if ($product->status === 'PENDING')
                                                    <form method="POST" action="{{ route('products.approve', $product) }}">
                                                        @csrf @method('PATCH')
                                                        <button class="btn btn-xs btn-success">✓</button>
                                                    </form>
                                                    <form method="POST" action="{{ route('products.reject', $product) }}">
                                                        @csrf @method('PATCH')
                                                        <button class="btn btn-xs btn-danger">✗</button>
                                                    </form>
                                                @endif
                                                <a href="{{ route('products.show', $product) }}" class="btn btn-xs btn-secondary">Detail</a>
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

    <script>
        // Toggle fee type fields
        const sel = document.getElementById('fee-type-select');
        const rateGroup = document.getElementById('fee-rate-group');
        const flatGroup = document.getElementById('fee-flat-group');
        function toggleFee() {
            if (sel.value === 'percentage') {
                rateGroup.style.display = '';
                flatGroup.style.display = 'none';
            } else {
                rateGroup.style.display = 'none';
                flatGroup.style.display = '';
            }
        }
        sel.addEventListener('change', toggleFee);
        toggleFee();
    </script>

@endsection
