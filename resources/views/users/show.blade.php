@extends('layouts.app')

@section('title', 'Detail Pengguna: ' . $user->name)
@section('page-title', 'Detail Pengguna')
@section('page-subtitle', $user->name)

@section('topbar-actions')
    <a href="{{ route('users.index') }}" class="btn btn-secondary btn-sm">← Kembali</a>
@endsection

@section('content')

    <div style="display:grid;grid-template-columns:320px 1fr;gap:20px;">

        {{-- Left: User Card --}}
        <div>
            <div class="card mb-4">
                <div class="card-body" style="text-align:center;">
                    <div
                        style="width:80px;height:80px;background:linear-gradient(135deg,var(--coffee-500),var(--coffee-800));border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:32px;font-weight:800;color:#fff;margin:0 auto 16px;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <h2 style="font-size:18px;font-weight:700;color:var(--text-primary);margin-bottom:4px;">
                        {{ $user->name }}</h2>
                    <div style="margin-bottom:12px;">
                        <span class="badge badge-{{ strtolower($user->role) }}">{{ $user->role }}</span>
                        @if ($user->is_verified)
                            <span class="badge badge-approved">✓ Verified</span>
                        @else
                            <span class="badge badge-pending">Unverified</span>
                        @endif
                    </div>
                    <div style="font-size:13px;color:var(--text-muted);margin-bottom:4px;">{{ $user->email }}</div>
                    @if ($user->whatsapp)
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $user->whatsapp) }}" target="_blank"
                            style="font-size:13px;color:var(--coffee-400);">📱 {{ $user->whatsapp }}</a>
                    @endif
                    <div style="margin-top:16px;font-size:12px;color:var(--text-muted);">Bergabung:
                        {{ $user->created_at->format('d M Y') }}</div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><span class="card-title">🎮 Aksi</span></div>
                <div class="card-body" style="display:flex;flex-direction:column;gap:8px;">
                    @if ($user->role === 'OWNER')
                        @php
                            $verifiedUmkmCount = $user->umkms()->where('is_verified', true)->count();
                        @endphp
                        @if (! $user->is_verified && $verifiedUmkmCount > 0)
                            <div style="padding:10px 12px;background:#450a0a;border:1px solid #7f1d1d;border-radius:6px;">
                                <p style="font-size:12px;color:#fecaca;margin:0;">
                                    <strong>Peringatan:</strong> Pengguna ini memiliki <strong>{{ $verifiedUmkmCount }} UMKM terverifikasi</strong>.
                                    Jika verifikasi pengguna dibatalkan, semua UMKM tersebut akan <strong>otomatis dibatalkan verifikasinya</strong> dan tidak bisa berjualan.
                                </p>
                            </div>
                        @endif
                        <form method="POST" action="{{ route('users.toggleVerify', $user) }}">
                            @csrf @method('PATCH')
                            <button class="btn {{ $user->is_verified ? 'btn-warning' : 'btn-success' }}"
                                style="width:100%;justify-content:center;">
                                {{ $user->is_verified ? '✗ Batalkan Verifikasi' : '✓ Verifikasi Pengguna' }}
                            </button>
                        </form>
                    @endif
                    @if (auth()->user()->isSuperAdmin() && $user->role !== 'SUPERADMIN')
                        <div class="divider"></div>
                        <form method="POST" action="{{ route('users.destroy', $user) }}"
                            onsubmit="return confirm('Yakin hapus pengguna ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger" style="width:100%;justify-content:center;">🗑 Hapus
                                Pengguna</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right --}}
        <div>
            @if ($user->umkms->isNotEmpty())
                <div class="card mb-4">
                    <div class="card-header"><span class="card-title">🏪 UMKM Dimiliki ({{ $user->umkms->count() }})</span>
                    </div>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Nama UMKM</th>
                                    <th>Produk</th>
                                    <th>Fee Platform</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($user->umkms as $umkm)
                                    <tr>
                                        <td class="td-primary">{{ $umkm->name }}</td>
                                        <td>{{ $umkm->products->count() }} produk</td>
                                        <td>
                                            @if ($umkm->platform_fee_type === 'percentage')
                                                {{ $umkm->platform_fee_rate }}%
                                            @else
                                                Rp {{ number_format($umkm->platform_fee_flat, 0, ',', '.') }}
                                            @endif
                                        </td>
                                        <td>
                                            @if ($umkm->is_verified)
                                                <span class="badge badge-success">Terverifikasi</span>
                                            @else
                                                <span class="badge badge-warning">Belum</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('umkms.show', $umkm) }}"
                                                class="btn btn-xs btn-secondary">Detail</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            @if ($user->orders->isNotEmpty())
                <div class="card mb-4">
                    <div class="card-header"><span class="card-title">🛒 Riwayat Pesanan
                            ({{ $user->orders->count() }})</span></div>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>#ID</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($user->orders->take(10) as $order)
                                    <tr>
                                        <td><span
                                                style="font-family:monospace;color:var(--coffee-400);">#{{ $order->id }}</span>
                                        </td>
                                        <td style="color:var(--coffee-400);font-weight:600;">Rp
                                            {{ number_format($order->total_price, 0, ',', '.') }}</td>
                                        <td><span
                                                class="badge badge-{{ strtolower($order->status) }}">{{ $order->status }}</span>
                                        </td>
                                        <td class="td-muted">{{ $order->created_at->format('d/m/Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            @if ($user->withdrawals->isNotEmpty())
                <div class="card">
                    <div class="card-header"><span class="card-title">💸 Riwayat Penarikan
                            ({{ $user->withdrawals->count() }})</span></div>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>#ID</th>
                                    <th>Jumlah</th>
                                    <th>Bank</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($user->withdrawals->take(10) as $wd)
                                    <tr>
                                        <td><span
                                                style="font-family:monospace;color:var(--coffee-400);">#{{ $wd->id }}</span>
                                        </td>
                                        <td style="color:var(--coffee-400);font-weight:600;">Rp
                                            {{ number_format($wd->amount, 0, ',', '.') }}</td>
                                        <td class="td-primary">{{ $wd->bank_name }} · {{ $wd->bank_account }}</td>
                                        <td><span
                                                class="badge badge-{{ strtolower($wd->status) }}">{{ $wd->status }}</span>
                                        </td>
                                        <td class="td-muted">{{ $wd->created_at->format('d/m/Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>

@endsection
