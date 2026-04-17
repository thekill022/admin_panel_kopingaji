@extends('layouts.app')

@section('title', 'Order #' . $order->id)
@section('page-title', 'Detail Pesanan')
@section('page-subtitle', 'Order #' . $order->id)

@section('topbar-actions')
    <a href="{{ route('orders.index') }}" class="btn btn-secondary btn-sm">← Kembali</a>
@endsection

@section('content')

    <style>
        .order-detail-wrap {
            display: grid;
            grid-template-columns: 1fr;
            gap: 24px;
        }

        @media (min-width: 1024px) {
            .order-detail-wrap {
                grid-template-columns: 1fr 380px;
            }
        }

        /* ── Order Header Banner ── */
        .order-banner {
            position: relative;
            overflow: hidden;
            border-radius: 1rem;
            padding: 2rem 2rem 1.75rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1.25rem;
        }
        .order-banner.status-completed { background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border: 1px solid #bbf7d0; }
        .order-banner.status-paid      { background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border: 1px solid #bfdbfe; }
        .order-banner.status-pending    { background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border: 1px solid #fde68a; }
        .order-banner.status-cancelled  { background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%); border: 1px solid #fecaca; }

        .order-banner::after {
            content: '';
            position: absolute;
            right: -40px;
            bottom: -40px;
            width: 180px;
            height: 180px;
            border-radius: 50%;
            opacity: 0.07;
        }
        .order-banner.status-completed::after { background: #16a34a; }
        .order-banner.status-paid::after      { background: #2563eb; }
        .order-banner.status-pending::after    { background: #d97706; }
        .order-banner.status-cancelled::after  { background: #dc2626; }

        .order-banner-left {
            display: flex;
            align-items: center;
            gap: 1rem;
            position: relative;
            z-index: 1;
        }
        .order-banner-icon {
            width: 56px;
            height: 56px;
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            flex-shrink: 0;
        }
        .status-completed .order-banner-icon { background: linear-gradient(135deg, #22c55e, #16a34a); }
        .status-paid .order-banner-icon      { background: linear-gradient(135deg, #3b82f6, #2563eb); }
        .status-pending .order-banner-icon   { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .status-cancelled .order-banner-icon { background: linear-gradient(135deg, #ef4444, #dc2626); }

        .order-banner-title {
            font-size: 1.25rem;
            font-weight: 800;
            color: #111827;
            letter-spacing: -0.025em;
        }
        .order-banner-subtitle {
            font-size: 0.8rem;
            color: #6b7280;
            margin-top: 2px;
        }
        .order-banner-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 0.5rem 1.25rem;
            border-radius: 9999px;
            font-size: 0.8rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            position: relative;
            z-index: 1;
        }
        .status-completed .order-banner-badge { background: #16a34a; color: white; }
        .status-paid .order-banner-badge      { background: #2563eb; color: white; }
        .status-pending .order-banner-badge   { background: #d97706; color: white; }
        .status-cancelled .order-banner-badge { background: #dc2626; color: white; }

        /* ── Item Cards ── */
        .order-item-row {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #f3f4f6;
            transition: background 0.15s;
        }
        .order-item-row:last-child { border-bottom: 0; }
        .order-item-row:hover { background: #fafafa; }

        .order-item-icon {
            width: 48px;
            height: 48px;
            border-radius: 0.75rem;
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            flex-shrink: 0;
        }
        .order-item-info { flex: 1; min-width: 0; }
        .order-item-name {
            font-size: 0.9rem;
            font-weight: 700;
            color: #111827;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .order-item-meta {
            font-size: 0.75rem;
            color: #9ca3af;
            margin-top: 2px;
        }
        .order-item-qty {
            background: #f3f4f6;
            color: #4b5563;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 0.25rem 0.75rem;
            border-radius: 0.5rem;
            flex-shrink: 0;
        }
        .order-item-subtotal {
            font-size: 0.95rem;
            font-weight: 800;
            color: #c4811f;
            text-align: right;
            flex-shrink: 0;
            min-width: 110px;
        }

        /* ── Total Footer ── */
        .order-total-footer {
            padding: 1.25rem 1.5rem;
            background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
            border-top: 2px solid #fde68a;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .order-total-label {
            font-size: 0.85rem;
            font-weight: 700;
            color: #92400e;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .order-total-value {
            font-size: 1.5rem;
            font-weight: 900;
            color: #92400e;
            letter-spacing: -0.025em;
        }

        /* ── Payment Card ── */
        .payment-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
        }
        .payment-grid-item {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #f3f4f6;
            border-right: 1px solid #f3f4f6;
        }
        .payment-grid-item:nth-child(even) { border-right: 0; }
        .payment-grid-item:nth-last-child(-n+2) { border-bottom: 0; }
        .payment-grid-label {
            font-size: 0.7rem;
            font-weight: 700;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 4px;
        }
        .payment-grid-value {
            font-size: 0.9rem;
            font-weight: 600;
            color: #111827;
        }

        /* ── Sidebar Info Cards ── */
        .info-card-body { padding: 1.25rem 1.5rem; }

        .profile-row {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .profile-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1rem;
            flex-shrink: 0;
        }
        .profile-name {
            font-size: 0.95rem;
            font-weight: 700;
            color: #111827;
        }
        .profile-email {
            font-size: 0.8rem;
            color: #9ca3af;
        }

        .umkm-row {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .umkm-icon {
            width: 48px;
            height: 48px;
            border-radius: 0.75rem;
            background: linear-gradient(135deg, #ede9fe, #ddd6fe);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            flex-shrink: 0;
        }

        .meta-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .meta-list li {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.65rem 0;
            border-bottom: 1px solid #f3f4f6;
            font-size: 0.8rem;
        }
        .meta-list li:last-child { border-bottom: 0; }
        .meta-list-label { color: #9ca3af; font-weight: 500; }
        .meta-list-value { color: #111827; font-weight: 700; }

        /* ── Status Form ── */
        .status-form-wrap {
            padding: 1.25rem 1.5rem;
            background: #f9fafb;
            border-top: 1px solid #f3f4f6;
        }
        .status-form-wrap label {
            display: block;
            font-size: 0.7rem;
            font-weight: 700;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 8px;
        }
        .status-form-wrap select {
            width: 100%;
            padding: 0.6rem 1rem;
            border-radius: 0.75rem;
            border: 1px solid #e5e7eb;
            font-size: 0.85rem;
            font-weight: 600;
            color: #374151;
            background: white;
            margin-bottom: 12px;
            transition: border-color 0.2s;
            cursor: pointer;
        }
        .status-form-wrap select:focus { border-color: #c4811f; outline: none; }

        /* ── WA Button ── */
        .wa-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
            padding: 0.65rem 1rem;
            border-radius: 0.75rem;
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: white;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.025em;
            transition: all 0.2s;
            text-decoration: none;
            margin-top: 1rem;
            border: 0;
        }
        .wa-btn:hover {
            background: linear-gradient(135deg, #16a34a, #15803d);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(22, 163, 74, 0.3);
        }

        /* ── QR Indicator ── */
        .qr-indicator {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 0.3rem 0.75rem;
            border-radius: 0.5rem;
        }
        .qr-indicator.scanned { background: #f0fdf4; color: #16a34a; }
        .qr-indicator.not-scanned { background: #f3f4f6; color: #9ca3af; }

        /* ── Timeline ── */
        .timeline-bar {
            padding: 1.25rem 1.5rem 0.75rem;
            display: flex;
            align-items: center;
            gap: 0;
        }
        .timeline-step {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
        }
        .timeline-dot {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.65rem;
            font-weight: 800;
            z-index: 1;
            transition: all 0.3s;
        }
        .timeline-dot.active { background: #c4811f; color: white; box-shadow: 0 0 0 4px rgba(196, 129, 31, 0.15); }
        .timeline-dot.done   { background: #22c55e; color: white; }
        .timeline-dot.inactive { background: #e5e7eb; color: #9ca3af; }
        .timeline-dot.cancelled { background: #ef4444; color: white; }
        .timeline-label {
            font-size: 0.6rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-top: 6px;
            color: #9ca3af;
        }
        .timeline-label.highlight { color: #c4811f; }
        .timeline-connector {
            flex: 1;
            height: 3px;
            background: #e5e7eb;
            margin: 0 -4px;
            margin-bottom: 20px;
        }
        .timeline-connector.done { background: #22c55e; }
    </style>

    {{-- ── Order Status Banner ── --}}
    @php
        $statusClass = strtolower($order->status);
        $statusIcons = [
            'COMPLETED' => 'fas fa-check-circle',
            'PAID'      => 'fas fa-credit-card',
            'PENDING'   => 'fas fa-clock',
            'CANCELLED' => 'fas fa-times-circle',
        ];
        $statusLabels = [
            'COMPLETED' => 'Pesanan Selesai',
            'PAID'      => 'Pembayaran Diterima',
            'PENDING'   => 'Menunggu Pembayaran',
            'CANCELLED' => 'Pesanan Dibatalkan',
        ];
    @endphp

    <div class="order-banner status-{{ $statusClass }}">
        <div class="order-banner-left">
            <div class="order-banner-icon">
                <i class="{{ $statusIcons[$order->status] ?? 'fas fa-shopping-bag' }}"></i>
            </div>
            <div>
                <div class="order-banner-title">Order #{{ $order->id }}</div>
                <div class="order-banner-subtitle">
                    {{ $order->created_at->format('d M Y, H:i') }} WIB · {{ $order->items->sum('quantity') }} item
                </div>
            </div>
        </div>
        <div class="order-banner-badge">
            <i class="{{ $statusIcons[$order->status] ?? 'fas fa-circle' }}"></i>
            {{ $statusLabels[$order->status] ?? $order->status }}
        </div>
    </div>

    {{-- ── Progress Timeline ── --}}
    @php
        $steps = ['PENDING', 'PAID', 'COMPLETED'];
        $isCancelled = $order->status === 'CANCELLED';
        $currentIdx = array_search($order->status, $steps);
        if ($currentIdx === false) $currentIdx = -1;
    @endphp
    @if(!$isCancelled)
        <div class="card" style="margin-top: -1px; border-top-left-radius: 0; border-top-right-radius: 0;">
            <div class="timeline-bar">
                @foreach($steps as $idx => $step)
                    @if($idx > 0)
                        <div class="timeline-connector {{ $idx <= $currentIdx ? 'done' : '' }}"></div>
                    @endif
                    <div class="timeline-step">
                        <div class="timeline-dot {{ $idx < $currentIdx ? 'done' : ($idx === $currentIdx ? 'active' : 'inactive') }}">
                            @if($idx < $currentIdx)
                                <i class="fas fa-check"></i>
                            @else
                                {{ $idx + 1 }}
                            @endif
                        </div>
                        <div class="timeline-label {{ $idx === $currentIdx ? 'highlight' : '' }}">{{ $step }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ── Main Grid ── --}}
    <div class="order-detail-wrap" style="margin-top: 24px;">

        {{-- ════ LEFT COLUMN ════ --}}
        <div>
            {{-- Order Items --}}
            <div class="card" style="margin-bottom: 24px;">
                <div class="card-header">
                    <span class="card-title"><i class="fas fa-receipt" style="color: #c4811f; margin-right: 6px;"></i> Item Pesanan</span>
                    <span style="font-size: 0.75rem; font-weight: 700; color: #9ca3af;">{{ $order->items->count() }} Produk</span>
                </div>

                @foreach($order->items as $item)
                    <div class="order-item-row">
                        <div class="order-item-icon">
                            @if($item->product && $item->product->image_url)
                                <img src="{{ asset('storage/' . $item->product->image_url) }}"
                                     style="width:100%;height:100%;object-fit:cover;border-radius:0.75rem;" alt="">
                            @else
                                ☕
                            @endif
                        </div>
                        <div class="order-item-info">
                            <div class="order-item-name">{{ $item->product->name ?? 'Produk Dihapus' }}</div>
                            <div class="order-item-meta">Rp {{ number_format($item->price, 0, ',', '.') }} / item</div>
                        </div>
                        <div class="order-item-qty">× {{ $item->quantity }}</div>
                        <div class="order-item-subtotal">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</div>
                    </div>
                @endforeach

                <div class="order-total-footer">
                    <div class="order-total-label">
                        <i class="fas fa-calculator" style="margin-right: 4px;"></i> Total Pesanan
                    </div>
                    <div class="order-total-value">Rp {{ number_format($order->total_price, 0, ',', '.') }}</div>
                </div>
            </div>

            {{-- Payment Info --}}
            <div class="card" style="margin-bottom: 24px;">
                <div class="card-header">
                    <span class="card-title"><i class="fas fa-credit-card" style="color: #3b82f6; margin-right: 6px;"></i> Informasi Pembayaran</span>
                    @if($order->payment && $order->payment->paid_at)
                        <span class="badge badge-completed" style="font-size: 0.65rem;"><i class="fas fa-check" style="margin-right:3px;font-size:0.55rem;"></i> Lunas</span>
                    @else
                        <span class="badge badge-pending" style="font-size: 0.65rem;">Belum Bayar</span>
                    @endif
                </div>
                @if($order->payment)
                    <div class="payment-grid">
                        <div class="payment-grid-item">
                            <div class="payment-grid-label">Provider</div>
                            <div class="payment-grid-value">
                                <i class="fas fa-building" style="color: #c4811f; margin-right: 4px; font-size: 0.7rem;"></i>
                                {{ $order->payment->provider }}
                            </div>
                        </div>
                        <div class="payment-grid-item">
                            <div class="payment-grid-label">Reference ID</div>
                            <div class="payment-grid-value" style="font-family: 'Courier New', monospace; font-size: 0.8rem; color: #6366f1;">
                                {{ $order->payment->reference_id ?? '—' }}
                            </div>
                        </div>
                        <div class="payment-grid-item">
                            <div class="payment-grid-label">Jumlah Dibayar</div>
                            <div class="payment-grid-value" style="color: #16a34a; font-weight: 800; font-size: 1rem;">
                                Rp {{ number_format($order->payment->amount, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="payment-grid-item">
                            <div class="payment-grid-label">Waktu Pembayaran</div>
                            <div class="payment-grid-value">
                                @if($order->payment->paid_at)
                                    <i class="far fa-clock" style="color: #9ca3af; margin-right: 4px; font-size: 0.7rem;"></i>
                                    {{ $order->payment->paid_at->format('d M Y, H:i') }}
                                @else
                                    <span style="color: #d1d5db;">—</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    <div style="padding: 3rem 2rem; text-align: center;">
                        <div style="font-size: 3rem; opacity: 0.15; margin-bottom: 0.75rem;"><i class="fas fa-receipt"></i></div>
                        <p style="color: #9ca3af; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Belum ada data pembayaran</p>
                    </div>
                @endif
            </div>

            {{-- Additional Info --}}
            <div class="card">
                <div class="card-header">
                    <span class="card-title"><i class="fas fa-info-circle" style="color: #6366f1; margin-right: 6px;"></i> Informasi Tambahan</span>
                </div>
                <div style="padding: 0;">
                    <ul class="meta-list" style="padding: 0 1.5rem;">
                        <li>
                            <span class="meta-list-label"><i class="fas fa-hashtag" style="margin-right: 6px; font-size: 0.7rem;"></i>Order ID</span>
                            <span class="meta-list-value" style="font-family: monospace; color: #c4811f;">#{{ $order->id }}</span>
                        </li>
                        <li>
                            <span class="meta-list-label"><i class="far fa-calendar" style="margin-right: 6px; font-size: 0.7rem;"></i>Tanggal Pesanan</span>
                            <span class="meta-list-value">{{ $order->created_at->format('d M Y, H:i') }}</span>
                        </li>
                        <li>
                            <span class="meta-list-label"><i class="fas fa-wallet" style="margin-right: 6px; font-size: 0.7rem;"></i>Metode Bayar</span>
                            <span class="meta-list-value">{{ $order->payment_method ?? '—' }}</span>
                        </li>
                        <li>
                            <span class="meta-list-label"><i class="fas fa-qrcode" style="margin-right: 6px; font-size: 0.7rem;"></i>QR Code</span>
                            <span>
                                @if($order->is_scanned)
                                    <span class="qr-indicator scanned"><i class="fas fa-check-circle"></i> Sudah Discan</span>
                                @else
                                    <span class="qr-indicator not-scanned"><i class="fas fa-minus-circle"></i> Belum Discan</span>
                                @endif
                            </span>
                        </li>
                        <li>
                            <span class="meta-list-label"><i class="far fa-clock" style="margin-right: 6px; font-size: 0.7rem;"></i>Terakhir Diubah</span>
                            <span class="meta-list-value">{{ $order->updated_at->diffForHumans() }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- ════ RIGHT COLUMN ════ --}}
        <div>

            {{-- Status & Action Card --}}
            <div class="card" style="margin-bottom: 20px;">
                <div class="card-header">
                    <span class="card-title"><i class="fas fa-sliders-h" style="color: #c4811f; margin-right: 6px;"></i> Kelola Status</span>
                </div>
                <div class="info-card-body" style="text-align: center; padding-bottom: 0;">
                    <div style="display: inline-flex; align-items: center; gap: 8px; padding: 0.75rem 1.5rem; border-radius: 1rem; margin-bottom: 0.5rem;
                        {{ $order->status === 'COMPLETED' ? 'background: #f0fdf4; border: 1px solid #bbf7d0;' : '' }}
                        {{ $order->status === 'PAID' ? 'background: #eff6ff; border: 1px solid #bfdbfe;' : '' }}
                        {{ $order->status === 'PENDING' ? 'background: #fffbeb; border: 1px solid #fde68a;' : '' }}
                        {{ $order->status === 'CANCELLED' ? 'background: #fef2f2; border: 1px solid #fecaca;' : '' }}
                    ">
                        <i class="{{ $statusIcons[$order->status] ?? 'fas fa-circle' }}" style="font-size: 1.1rem;
                            {{ $order->status === 'COMPLETED' ? 'color: #16a34a;' : '' }}
                            {{ $order->status === 'PAID' ? 'color: #2563eb;' : '' }}
                            {{ $order->status === 'PENDING' ? 'color: #d97706;' : '' }}
                            {{ $order->status === 'CANCELLED' ? 'color: #dc2626;' : '' }}
                        "></i>
                        <span style="font-size: 1rem; font-weight: 800;
                            {{ $order->status === 'COMPLETED' ? 'color: #16a34a;' : '' }}
                            {{ $order->status === 'PAID' ? 'color: #2563eb;' : '' }}
                            {{ $order->status === 'PENDING' ? 'color: #d97706;' : '' }}
                            {{ $order->status === 'CANCELLED' ? 'color: #dc2626;' : '' }}
                        ">{{ $order->status }}</span>
                    </div>
                </div>
                <div class="status-form-wrap">
                    <form method="POST" action="{{ route('orders.updateStatus', $order) }}">
                        @csrf @method('PATCH')
                        <label for="order-status-select">Ubah Status Pesanan</label>
                        <select name="status" id="order-status-select">
                            @foreach(['PENDING','PAID','COMPLETED','CANCELLED'] as $s)
                                <option value="{{ $s }}" {{ $order->status === $s ? 'selected' : '' }}>{{ $s }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">
                            <i class="fas fa-save"></i> Simpan Status
                        </button>
                    </form>
                </div>
            </div>

            {{-- Buyer Card --}}
            <div class="card" style="margin-bottom: 20px;">
                <div class="card-header">
                    <span class="card-title"><i class="fas fa-user" style="color: #6366f1; margin-right: 6px;"></i> Pembeli</span>
                    <a href="{{ route('users.show', $order->buyer) }}" class="btn btn-xs btn-secondary" style="font-size:0.65rem;">Detail</a>
                </div>
                <div class="info-card-body">
                    <div class="profile-row">
                        <div class="profile-avatar" style="background: linear-gradient(135deg, #e0e7ff, #c7d2fe); color: #4f46e5;">
                            {{ strtoupper(substr($order->buyer->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="profile-name">{{ $order->buyer->name }}</div>
                            <div class="profile-email">{{ $order->buyer->email }}</div>
                        </div>
                    </div>

                    @if($order->whatsapp)
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->whatsapp) }}" target="_blank" class="wa-btn">
                            <i class="fab fa-whatsapp" style="font-size: 1rem;"></i> Chat WhatsApp
                        </a>
                    @endif
                </div>
            </div>

            {{-- UMKM Card --}}
            <div class="card">
                <div class="card-header">
                    <span class="card-title"><i class="fas fa-store" style="color: #8b5cf6; margin-right: 6px;"></i> UMKM</span>
                    <a href="{{ route('umkms.show', $order->umkm) }}" class="btn btn-xs btn-secondary" style="font-size:0.65rem;">Detail</a>
                </div>
                <div class="info-card-body">
                    <div class="umkm-row" style="margin-bottom: 1rem;">
                        <div class="umkm-icon">🏪</div>
                        <div>
                            <div style="font-size: 0.95rem; font-weight: 700; color: #111827;">{{ $order->umkm->name }}</div>
                            <div style="font-size: 0.8rem; color: #9ca3af;">
                                <i class="fas fa-user-tie" style="margin-right: 4px; font-size: 0.65rem;"></i>
                                {{ $order->umkm->owner->name ?? '—' }}
                            </div>
                        </div>
                    </div>
                    <ul class="meta-list">
                        <li>
                            <span class="meta-list-label">Verifikasi</span>
                            <span>
                                @if($order->umkm->is_verified)
                                    <span class="badge badge-completed" style="font-size: 0.6rem;"><i class="fas fa-check" style="margin-right:2px;font-size:0.5rem;"></i> Terverifikasi</span>
                                @else
                                    <span class="badge badge-pending" style="font-size: 0.6rem;">Belum</span>
                                @endif
                            </span>
                        </li>
                        <li>
                            <span class="meta-list-label">Fee Platform</span>
                            <span class="meta-list-value" style="color: #c4811f;">
                                @if($order->umkm->platform_fee_type === 'percentage')
                                    {{ $order->umkm->platform_fee_rate }}%
                                @else
                                    Rp {{ number_format($order->umkm->platform_fee_flat, 0, ',', '.') }}
                                @endif
                            </span>
                        </li>
                        <li>
                            <span class="meta-list-label">Metode Bayar</span>
                            <span class="meta-list-value">{{ $order->payment_method ?? '—' }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

@endsection
