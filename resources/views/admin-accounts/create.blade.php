@extends('layouts.app')

@section('title', 'Tambah Admin')
@section('page-title', 'Buat Akun Admin Baru')
@section('page-subtitle', 'Tambahkan admin untuk membantu mengelola platform')

@section('topbar-actions')
    <a href="{{ route('admin-accounts.index') }}" class="btn btn-secondary btn-sm">← Kembali</a>
@endsection

@section('content')

<div style="max-width:600px;">
    <div class="card">
        <div class="card-header">
            <span class="card-title">👤 Informasi Akun Admin</span>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin-accounts.store') }}">
                @csrf

                <div class="form-group">
                    <label class="form-label required" for="name">Nama Lengkap</label>
                    <input type="text" id="name" name="name" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                           value="{{ old('name') }}" placeholder="Contoh: Admin Jakarta" required />
                    @error('name')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label required" for="email">Alamat Email</label>
                    <input type="email" id="email" name="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                           value="{{ old('email') }}" placeholder="admin@kopingaji.com" required />
                    @error('email')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="whatsapp">Nomor WhatsApp</label>
                    <input type="text" id="whatsapp" name="whatsapp" class="form-control {{ $errors->has('whatsapp') ? 'is-invalid' : '' }}"
                           value="{{ old('whatsapp') }}" placeholder="628xxxxxxxxxx" />
                    <div class="form-hint">Format: 628xxxxxxxxxx (tanpa spasi atau tanda +)</div>
                    @error('whatsapp')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="divider"></div>

                <div class="form-group">
                    <label class="form-label required" for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                           placeholder="Minimal 8 karakter" required />
                    @error('password')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label required" for="password_confirmation">Konfirmasi Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                           class="form-control" placeholder="Ulangi password" required />
                </div>

                <div style="background:rgba(196,129,31,0.08);border:1px solid rgba(196,129,31,0.2);border-radius:10px;padding:12px 16px;margin-bottom:20px;">
                    <div style="font-size:13px;color:var(--coffee-400);font-weight:600;margin-bottom:4px;">ℹ️ Catatan</div>
                    <div style="font-size:12px;color:var(--text-muted);">Akun admin yang dibuat akan memiliki akses ke: Manajemen Produk, Pesanan, Penarikan, UMKM, dan Pengguna. Akun admin <strong style="color:var(--text-secondary);">tidak bisa</strong> membuat admin baru (hanya SuperAdmin).</div>
                </div>

                <div style="display:flex;gap:10px;">
                    <button type="submit" class="btn btn-primary">✓ Buat Akun Admin</button>
                    <a href="{{ route('admin-accounts.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
