@extends('layouts.app')

@section('title', 'Kelola Admin')
@section('page-title', 'Kelola Akun Admin')
@section('page-subtitle', 'Buat dan kelola akun admin platform')

@section('topbar-actions')
    <a href="{{ route('admin-accounts.create') }}" class="btn btn-primary">+ Tambah Admin</a>
@endsection

@section('content')

<div class="card">
    <div class="card-header">
        <span class="card-title">🔑 Daftar Admin</span>
        <span style="font-size:13px;color:var(--text-muted);">Total: {{ $admins->total() }} admin</span>
    </div>

<style>
    .admin-tbl { table-layout: fixed; width: 100%; }
    .admin-tbl th,
    .admin-tbl td {
        padding: 0.75rem 0.75rem;
        font-size: 0.8rem;
        vertical-align: middle;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .admin-tbl th { font-size: 0.65rem; padding: 0.6rem 0.75rem; }

    .admin-tbl .col-admin   { width: 25%; }
    .admin-tbl .col-email   { width: 20%; }
    .admin-tbl .col-wa      { width: 15%; }
    .admin-tbl .col-status  { width: 12%; text-align: center; }
    .admin-tbl .col-date    { width: 12%; }
    .admin-tbl .col-action  { width: 16%; overflow: visible; white-space: normal; text-align: right; }

    .admin-tbl .action-flex {
        display: flex;
        gap: 5px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }
    .admin-tbl .action-flex .btn { font-size: 0.6rem; padding: 0.2rem 0.6rem; }
    
    .td-primary { font-weight:700; color:#111827; overflow:hidden; text-overflow:ellipsis; }
    .td-muted { font-size:0.7rem; color:#6b7280; overflow:hidden; text-overflow:ellipsis; }

    @media (max-width: 768px) {
        .admin-tbl .col-email, .admin-tbl .col-wa, .admin-tbl .col-date { display: none; }
        .admin-tbl .col-admin { width: 50%; }
        .admin-tbl .col-status { width: 25%; text-align: right; }
        .admin-tbl .col-action { width: 25%; }
        .admin-tbl th, .admin-tbl td { font-size: 0.7rem; padding: 0.4rem 0.5rem; }
        .admin-tbl th.col-status { text-align: right; }
    }
</style>

    <div class="table-wrap">
        @if($admins->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">👤</div>
                <p>Belum ada akun admin. Buat yang pertama!</p>
                <br>
                <a href="{{ route('admin-accounts.create') }}" class="btn btn-primary">+ Tambah Admin</a>
            </div>
        @else
        <table class="admin-tbl">
            <thead>
                <tr>
                    <th class="col-admin">Admin</th>
                    <th class="col-email">Email</th>
                    <th class="col-wa">WhatsApp</th>
                    <th class="col-status">Status</th>
                    <th class="col-date">Bergabung</th>
                    <th class="col-action">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($admins as $admin)
                <tr>
                    <td class="col-admin">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div class="avatar-sm shrink-0">{{ strtoupper(substr($admin->name,0,1)) }}</div>
                            <div style="min-width:0;flex:1;">
                                <div class="td-primary">{{ $admin->name }}</div>
                                <span class="badge badge-admin" style="font-size:0.6rem;padding:0.1rem 0.4rem;">ADMIN</span>
                            </div>
                        </div>
                    </td>
                    <td class="col-email">
                        <div class="td-primary">{{ $admin->email }}</div>
                    </td>
                    <td class="col-wa">
                        @if($admin->whatsapp)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $admin->whatsapp) }}"
                               target="_blank"
                               style="color:var(--coffee-400);text-decoration:none;">
                                📱 {{ $admin->whatsapp }}
                            </a>
                        @else
                            <span class="td-muted">—</span>
                        @endif
                    </td>
                    <td class="col-status">
                        @if($admin->is_verified)
                            <span class="badge badge-approved" style="font-size:0.6rem;padding:0.2rem 0.5rem;">✓ Terverifikasi</span>
                        @else
                            <span class="badge badge-pending" style="font-size:0.6rem;padding:0.2rem 0.5rem;">Belum Verified</span>
                        @endif
                    </td>
                    <td class="col-date td-muted">{{ $admin->created_at->format('d M Y') }}</td>
                    <td class="col-action">
                        <div class="action-flex">
                            <a href="{{ route('admin-accounts.edit', $admin) }}" class="btn btn-warning">✏ Edit</a>
                            <form method="POST" action="{{ route('admin-accounts.destroy', $admin) }}"
                                  onsubmit="return confirm('Yakin hapus akun admin {{ $admin->name }}?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger">🗑 Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="pagination-wrap">
            <span>Menampilkan {{ $admins->firstItem() }}–{{ $admins->lastItem() }} dari {{ $admins->total() }} admin</span>
            <div class="pagination">
                @if($admins->onFirstPage())
                    <span class="page-link disabled">‹</span>
                @else
                    <a href="{{ $admins->previousPageUrl() }}" class="page-link">‹</a>
                @endif
                @foreach($admins->getUrlRange(max(1,$admins->currentPage()-2), min($admins->lastPage(),$admins->currentPage()+2)) as $page => $url)
                    <a href="{{ $url }}" class="page-link {{ $page === $admins->currentPage() ? 'active' : '' }}">{{ $page }}</a>
                @endforeach
                @if($admins->hasMorePages())
                    <a href="{{ $admins->nextPageUrl() }}" class="page-link">›</a>
                @else
                    <span class="page-link disabled">›</span>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

@endsection
