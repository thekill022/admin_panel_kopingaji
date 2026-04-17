@extends('layouts.app')

@section('title', 'Detail Refund #' . $refund->id)
@section('page-title', 'Detail Refund')
@section('page-subtitle', 'Refund #' . $refund->id)

@section('topbar-actions')
    <a href="{{ route('refunds.index') }}" class="btn btn-secondary btn-sm">← Kembali</a>
@endsection

@section('content')

<style>
    .refund-grid { display: grid; grid-template-columns: 1fr; gap: 24px; }
    @media (min-width: 1024px) { .refund-grid { grid-template-columns: 360px 1fr; } }

    .rf-banner { position: relative; overflow: hidden; border-radius: 1rem; padding: 2rem; text-align: center; }
    .rf-banner.pending { background: linear-gradient(135deg, #fffbeb, #fef3c7, #fde68a); border: 1px solid #fde68a; }
    .rf-banner.approved { background: linear-gradient(135deg, #f0fdf4, #dcfce7, #bbf7d0); border: 1px solid #bbf7d0; }
    .rf-banner.rejected { background: linear-gradient(135deg, #fef2f2, #fecaca, #fca5a5); border: 1px solid #fecaca; }
    .rf-banner-icon { width: 72px; height: 72px; margin: 0 auto 12px; border-radius: 1.25rem; display: flex; align-items: center; justify-content: center; font-size: 1.75rem; color: white; box-shadow: 0 8px 24px rgba(0,0,0,0.15); }
    .rf-banner-icon.pending { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .rf-banner-icon.approved { background: linear-gradient(135deg, #22c55e, #16a34a); }
    .rf-banner-icon.rejected { background: linear-gradient(135deg, #ef4444, #dc2626); }
    .rf-banner-title { font-size: 1.25rem; font-weight: 800; color: #111827; margin-bottom: 4px; }
    .rf-banner-sub { font-size: 0.8rem; color: #6b7280; }

    .rf-meta-list { list-style: none; padding: 0; margin: 0; }
    .rf-meta-list li { display: flex; align-items: center; justify-content: space-between; padding: 0.7rem 0; border-bottom: 1px solid #f3f4f6; font-size: 0.8rem; }
    .rf-meta-list li:last-child { border-bottom: 0; }
    .rf-meta-label { color: #9ca3af; font-weight: 500; }
    .rf-meta-value { color: #111827; font-weight: 700; }

    .rf-reason-box { padding: 1rem; background: #f9fafb; border-radius: 0.75rem; border: 1px solid #f3f4f6; margin-top: 1rem; }
    .rf-reason-title { font-size: 0.7rem; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px; }
    .rf-reason-text { font-size: 0.85rem; color: #374151; line-height: 1.6; }

    .rf-item-card { display: flex; align-items: center; gap: 12px; padding: 0.85rem 1rem; background: #f9fafb; border-radius: 0.75rem; border: 1px solid #f3f4f6; margin-bottom: 8px; }
    .rf-item-card:last-child { margin-bottom: 0; }
    .rf-item-img { width: 48px; height: 48px; border-radius: 0.6rem; background: #e5e7eb; display: flex; align-items: center; justify-content: center; color: #9ca3af; flex-shrink: 0; overflow: hidden; }
    .rf-item-img img { width: 100%; height: 100%; object-fit: cover; }
    .rf-item-name { font-weight: 700; color: #111827; font-size: 0.85rem; }
    .rf-item-detail { font-size: 0.72rem; color: #9ca3af; }
</style>

<div class="refund-grid">
    {{-- LEFT COLUMN --}}
    <div>
        {{-- Status Banner --}}
        <div class="rf-banner {{ strtolower($refund->status) }}" style="margin-bottom: 20px;">
            <div class="rf-banner-icon {{ strtolower($refund->status) }}">
                @if($refund->status === 'PENDING')
                    <i class="fas fa-clock"></i>
                @elseif($refund->status === 'APPROVED')
                    <i class="fas fa-check-circle"></i>
                @else
                    <i class="fas fa-times-circle"></i>
                @endif
            </div>
            <div class="rf-banner-title">
                @if($refund->status === 'PENDING') Menunggu Keputusan
                @elseif($refund->status === 'APPROVED') Refund Disetujui
                @else Refund Ditolak
                @endif
            </div>
            <div class="rf-banner-sub">Refund #{{ $refund->id }} • {{ $refund->created_at->format('d M Y, H:i') }}</div>
        </div>

        {{-- Info Card --}}
        <div class="card" style="margin-bottom: 20px;">
            <div class="card-header">
                <span class="card-title"><i class="fas fa-info-circle" style="color:#c4811f;margin-right:6px;"></i> Informasi Refund</span>
            </div>
            <div style="padding: 1.25rem 1.5rem;">
                <ul class="rf-meta-list">
                    <li>
                        <span class="rf-meta-label"><i class="fas fa-hashtag" style="margin-right:6px;font-size:0.7rem;"></i> ID Refund</span>
                        <span class="rf-meta-value">#{{ $refund->id }}</span>
                    </li>
                    <li>
                        <span class="rf-meta-label"><i class="fas fa-shopping-bag" style="margin-right:6px;font-size:0.7rem;"></i> Order</span>
                        <a href="{{ route('orders.show', $refund->order_id) }}" class="rf-meta-value" style="color:#c4811f;text-decoration:none;">#{{ $refund->order_id }}</a>
                    </li>
                    <li>
                        <span class="rf-meta-label"><i class="fas fa-money-bill-wave" style="margin-right:6px;font-size:0.7rem;"></i> Jumlah</span>
                        <span class="rf-meta-value" style="color:#dc2626;">Rp {{ number_format($refund->amount, 0, ',', '.') }}</span>
                    </li>
                    <li>
                        <span class="rf-meta-label"><i class="fas fa-user" style="margin-right:6px;font-size:0.7rem;"></i> Diajukan Oleh</span>
                        <span>
                            @if($refund->requested_by === 'BUYER')
                                <span class="badge badge-buyer" style="font-size:0.6rem;">Pembeli</span>
                            @else
                                <span class="badge badge-owner" style="font-size:0.6rem;">Penjual</span>
                            @endif
                        </span>
                    </li>
                    <li>
                        <span class="rf-meta-label"><i class="far fa-calendar" style="margin-right:6px;font-size:0.7rem;"></i> Tanggal Pengajuan</span>
                        <span class="rf-meta-value">{{ $refund->created_at->format('d M Y, H:i') }}</span>
                    </li>
                    @if($refund->refunded_at)
                    <li>
                        <span class="rf-meta-label"><i class="fas fa-check" style="margin-right:6px;font-size:0.7rem;"></i> Tanggal Refund</span>
                        <span class="rf-meta-value" style="color:#16a34a;">{{ $refund->refunded_at->format('d M Y, H:i') }}</span>
                    </li>
                    @endif
                </ul>

                {{-- Reason --}}
                <div class="rf-reason-box">
                    <div class="rf-reason-title"><i class="fas fa-comment-alt" style="margin-right:4px;"></i> Alasan Refund</div>
                    <div class="rf-reason-text">{{ $refund->reason }}</div>
                </div>

                {{-- Action Buttons --}}
                @if($refund->status === 'PENDING')
                <div style="display:flex;gap:10px;margin-top:1rem;">
                    <form method="POST" action="{{ route('refunds.approve', $refund) }}" style="flex:1;">
                        @csrf @method('PATCH')
                        <button class="btn btn-success" style="width:100%;justify-content:center;" onclick="return confirm('Setujui refund ini? Order akan diubah ke status REFUNDED.')">
                            <i class="fas fa-check-circle"></i> Setujui Refund
                        </button>
                    </form>
                    <form method="POST" action="{{ route('refunds.reject', $refund) }}" style="flex:1;">
                        @csrf @method('PATCH')
                        <button class="btn btn-danger" style="width:100%;justify-content:center;" onclick="return confirm('Tolak pengajuan refund ini?')">
                            <i class="fas fa-times-circle"></i> Tolak Refund
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- RIGHT COLUMN --}}
    <div>
        {{-- Buyer & UMKM Info --}}
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px;">
            {{-- Buyer --}}
            <div class="card">
                <div class="card-header">
                    <span class="card-title"><i class="fas fa-user" style="color:#c4811f;margin-right:6px;"></i> Pembeli</span>
                </div>
                <div style="padding:1.25rem 1.5rem;">
                    <div style="font-weight:800;color:#111827;font-size:1rem;margin-bottom:4px;">{{ $refund->order->buyer->name ?? '-' }}</div>
                    <div style="font-size:0.78rem;color:#6b7280;margin-bottom:2px;">{{ $refund->order->buyer->email ?? '' }}</div>
                    @if($refund->order->buyer->whatsapp ?? null)
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/','', $refund->order->buyer->whatsapp) }}" target="_blank" style="font-size:0.78rem;color:#c4811f;text-decoration:none;">
                            <i class="fab fa-whatsapp"></i> {{ $refund->order->buyer->whatsapp }}
                        </a>
                    @endif
                </div>
            </div>

            {{-- UMKM --}}
            <div class="card">
                <div class="card-header">
                    <span class="card-title"><i class="fas fa-store" style="color:#c4811f;margin-right:6px;"></i> UMKM</span>
                </div>
                <div style="padding:1.25rem 1.5rem;">
                    <div style="font-weight:800;color:#111827;font-size:1rem;margin-bottom:4px;">{{ $refund->order->umkm->name ?? '-' }}</div>
                    <div style="font-size:0.78rem;color:#6b7280;">Owner: {{ $refund->order->umkm->owner->name ?? '-' }}</div>
                </div>
            </div>
        </div>

        {{-- Order Items --}}
        <div class="card" style="margin-bottom:24px;">
            <div class="card-header">
                <span class="card-title"><i class="fas fa-box" style="color:#c4811f;margin-right:6px;"></i> Item Pesanan (Order #{{ $refund->order_id }})</span>
                <span class="badge badge-{{ strtolower($refund->order->status ?? 'pending') }}">{{ $refund->order->status ?? '-' }}</span>
            </div>
            <div style="padding:1.25rem 1.5rem;">
                @if($refund->order->items->isEmpty())
                    <p style="color:#9ca3af;font-size:0.8rem;">Tidak ada item.</p>
                @else
                    @foreach($refund->order->items as $item)
                    <div class="rf-item-card">
                        <div class="rf-item-img">
                            @if($item->product && $item->product->image_url)
                                <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}">
                            @else
                                <i class="fas fa-box"></i>
                            @endif
                        </div>
                        <div style="flex:1;min-width:0;">
                            <div class="rf-item-name">{{ $item->product->name ?? 'Produk Dihapus' }}</div>
                            <div class="rf-item-detail">{{ $item->quantity }}x @ Rp {{ number_format($item->price, 0, ',', '.') }}</div>
                        </div>
                        <div style="font-weight:700;color:#111827;font-size:0.85rem;white-space:nowrap;">
                            Rp {{ number_format($item->quantity * $item->price, 0, ',', '.') }}
                        </div>
                    </div>
                    @endforeach

                    <div style="margin-top:12px;padding-top:12px;border-top:2px solid #f3f4f6;display:flex;justify-content:space-between;align-items:center;">
                        <span style="font-weight:700;color:#6b7280;font-size:0.8rem;">Total Order</span>
                        <span style="font-weight:800;color:#111827;font-size:1rem;">Rp {{ number_format($refund->order->total_price, 0, ',', '.') }}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-top:6px;">
                        <span style="font-weight:700;color:#dc2626;font-size:0.8rem;">Jumlah Refund</span>
                        <span style="font-weight:800;color:#dc2626;font-size:1rem;">Rp {{ number_format($refund->amount, 0, ',', '.') }}</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Payment Info --}}
        @if($refund->order->payment)
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fas fa-credit-card" style="color:#c4811f;margin-right:6px;"></i> Informasi Pembayaran</span>
            </div>
            <div style="padding:1.25rem 1.5rem;">
                <ul class="rf-meta-list">
                    <li>
                        <span class="rf-meta-label">Provider</span>
                        <span class="rf-meta-value">{{ $refund->order->payment->provider ?? '-' }}</span>
                    </li>
                    <li>
                        <span class="rf-meta-label">Reference ID</span>
                        <span class="rf-meta-value" style="font-family:monospace;font-size:0.75rem;">{{ $refund->order->payment->reference_id ?? '-' }}</span>
                    </li>
                    <li>
                        <span class="rf-meta-label">Jumlah Bayar</span>
                        <span class="rf-meta-value" style="color:#16a34a;">Rp {{ number_format($refund->order->payment->amount ?? 0, 0, ',', '.') }}</span>
                    </li>
                    @if($refund->order->payment->paid_at)
                    <li>
                        <span class="rf-meta-label">Tanggal Bayar</span>
                        <span class="rf-meta-value">{{ $refund->order->payment->paid_at->format('d M Y, H:i') }}</span>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
        @endif
    </div>
</div>

@endsection
