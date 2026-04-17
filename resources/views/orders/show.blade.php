@extends('layouts.app')

@section('title', 'Order #' . $order->id)
@section('page-title', 'Detail Pesanan')
@section('page-subtitle', 'Order #' . $order->id)

@section('topbar-actions')
    <a href="{{ route('orders.index') }}" class="btn btn-secondary btn-sm">← Kembali</a>
@endsection

@section('content')

<div style="display:grid;grid-template-columns:1fr 360px;gap:20px;">
    <div>
        {{-- Order Items --}}
        <div class="card mb-4">
            <div class="card-header"><span class="card-title">📋 Item Pesanan</span></div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Qty</th>
                            <th>Harga Satuan</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td>
                                <div class="td-primary">{{ $item->product->name ?? 'Produk Dihapus' }}</div>
                            </td>
                            <td class="td-primary">{{ $item->quantity }}</td>
                            <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                            <td style="color:var(--coffee-400);font-weight:700;">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div style="padding:16px 20px;text-align:right;border-top:1px solid var(--dark-600);">
                    <span style="font-size:13px;color:var(--text-muted);">Total: </span>
                    <span style="font-size:20px;font-weight:800;color:var(--coffee-400);">Rp {{ number_format($order->total_price,0,',','.') }}</span>
                </div>
            </div>
        </div>

        {{-- Payment Info --}}
        <div class="card">
            <div class="card-header"><span class="card-title">💳 Informasi Pembayaran</span></div>
            <div class="card-body">
                @if($order->payment)
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;font-size:14px;">
                        <div><div class="td-muted">Provider</div><div class="td-primary">{{ $order->payment->provider ?? '—' }}</div></div>
                        <div><div class="td-muted">Reference ID</div><div class="td-primary">{{ $order->payment->reference_id ?? '—' }}</div></div>
                        <div><div class="td-muted">Jumlah</div><div style="color:var(--coffee-400);font-weight:700;">Rp {{ number_format($order->payment->amount,0,',','.') }}</div></div>
                        <div><div class="td-muted">Dibayar Pada</div><div class="td-primary">{{ $order->payment->paid_at ? $order->payment->paid_at->format('d M Y H:i') : '—' }}</div></div>
                    </div>
                @else
                    <div style="color:var(--text-muted);font-size:13px;">Belum ada data pembayaran.</div>
                @endif
            </div>
        </div>
    </div>

    {{-- Right Panel --}}
    <div>
        <div class="card mb-4">
            <div class="card-header"><span class="card-title">📊 Status Pesanan</span></div>
            <div class="card-body">
                <div style="text-align:center;margin-bottom:20px;">
                    <span class="badge badge-{{ strtolower($order->status) }}" style="font-size:15px;padding:8px 20px;">{{ $order->status }}</span>
                </div>
                <form method="POST" action="{{ route('orders.updateStatus', $order) }}">
                    @csrf @method('PATCH')
                    <div class="form-group">
                        <label class="form-label">Ubah Status</label>
                        <select name="status" class="form-control">
                            @foreach(['PENDING','PAID','COMPLETED','CANCELLED'] as $s)
                                <option value="{{ $s }}" {{ $order->status === $s ? 'selected' : '' }}>{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">Simpan Status</button>
                </form>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header"><span class="card-title">👤 Pembeli</span></div>
            <div class="card-body">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;">
                    <div class="avatar-sm">{{ strtoupper(substr($order->buyer->name,0,1)) }}</div>
                    <div>
                        <div class="td-primary">{{ $order->buyer->name }}</div>
                        <div class="td-muted">{{ $order->buyer->email }}</div>
                    </div>
                </div>
                @if($order->whatsapp)
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/','',$order->whatsapp) }}" target="_blank"
                       class="btn btn-success" style="width:100%;justify-content:center;">📱 Chat WhatsApp</a>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header"><span class="card-title">🏪 UMKM</span></div>
            <div class="card-body">
                <div class="td-primary" style="font-size:15px;margin-bottom:4px;">{{ $order->umkm->name }}</div>
                <div class="td-muted">Owner: {{ $order->umkm->owner->name ?? '—' }}</div>
                <div class="divider"></div>
                <div style="font-size:13px;color:var(--text-muted);">
                    Metode Bayar: <span style="color:var(--text-primary);">{{ $order->payment_method }}</span><br>
                    QR Scanned: <span style="color:{{ $order->is_scanned ? 'var(--success)' : 'var(--text-muted)' }}">{{ $order->is_scanned ? 'Ya' : 'Belum' }}</span>
                </div>
                <div style="margin-top:12px;">
                    <a href="{{ route('umkms.show', $order->umkm) }}" class="btn btn-secondary btn-sm">Lihat UMKM</a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
