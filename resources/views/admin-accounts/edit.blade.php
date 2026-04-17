@extends('layouts.app')

@section('title', 'Edit Admin: ' . $admin->name)
@section('page-title', 'Edit Akun Admin')
@section('page-subtitle', 'Edit akun: ' . $admin->name)

@section('topbar-actions')
    <a href="{{ route('admin-accounts.index') }}" class="btn btn-secondary btn-sm">← Kembali</a>
@endsection

@section('content')

<div style="max-width:600px;">
    <div class="card">
        <div class="card-header">
            <div style="display:flex;align-items:center;gap:10px;">
                <div class="avatar-sm" style="width:40px;height:40px;font-size:16px;">{{ strtoupper(substr($admin->name,0,1)) }}</div>
                <div>
                    <span class="card-title">{{ $admin->name }}</span>
                    <div style="font-size:12px;color:var(--text-muted);">{{ $admin->email }}</div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin-accounts.update', $admin) }}">
                @csrf @method('PUT')

                <div class="form-group">
                    <label class="form-label required" for="name">Nama Lengkap</label>
                    <input type="text" id="name" name="name" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                           value="{{ old('name', $admin->name) }}" required />
                    @error('name')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label required" for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                           value="{{ old('email', $admin->email) }}" required />
                    @error('email')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="whatsapp">WhatsApp</label>
                    <input type="text" id="whatsapp" name="whatsapp" class="form-control"
                           value="{{ old('whatsapp', $admin->whatsapp) }}" placeholder="628xxxxxxxxxx" />
                    @error('whatsapp')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="divider"></div>

                <div style="background:var(--dark-700);border-radius:10px;padding:12px 16px;margin-bottom:16px;">
                    <div style="font-size:12px;color:var(--text-muted);">Kosongkan field password jika tidak ingin mengubah password.</div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password Baru</label>
                    <input type="password" id="password" name="password" class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                           placeholder="Biarkan kosong jika tidak diubah" />
                    @error('password')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="password_confirmation">Konfirmasi Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control"
                           placeholder="Ulangi password baru" />
                </div>

                <div style="display:flex;gap:10px;">
                    <button type="submit" class="btn btn-primary">💾 Simpan Perubahan</button>
                    <a href="{{ route('admin-accounts.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
