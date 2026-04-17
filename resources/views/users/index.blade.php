@extends('layouts.app')

@section('title', 'Manajemen Pengguna')
@section('page-title', 'Manajemen Pengguna')
@section('page-subtitle', 'Kelola semua pengguna platform')

@section('content')

<style>
    .user-tbl { table-layout: fixed; width: 100%; }
    .user-tbl th,
    .user-tbl td {
        padding: 0.75rem 0.75rem;
        font-size: 0.8rem;
        vertical-align: middle;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .user-tbl th { font-size: 0.65rem; padding: 0.6rem 0.75rem; }

    /* 6 columns — balanced across full width */
    .user-tbl .col-user   { width: 22%; }
    .user-tbl .col-wa     { width: 16%; }
    .user-tbl .col-role   { width: 11%; text-align: center; }
    .user-tbl .col-status { width: 11%; text-align: center; }
    .user-tbl .col-date   { width: 12%; text-align: center; }
    .user-tbl .col-action { width: 28%; overflow: visible; white-space: normal; text-align: right; }

    .user-tbl .user-name { font-weight: 700; color: #111827; overflow: hidden; text-overflow: ellipsis; }
    .user-tbl .user-email { font-size: 0.7rem; color: #9ca3af; overflow: hidden; text-overflow: ellipsis; }

    .user-tbl .action-flex {
        display: flex;
        gap: 5px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }
    .user-tbl .action-flex .btn { font-size: 0.6rem; padding: 0.2rem 0.5rem; white-space: nowrap; }

    @media (max-width: 768px) {
        .user-tbl .col-wa, .user-tbl .col-date { display: none; }
        .user-tbl .col-user { width: 40%; }
        .user-tbl .col-role { width: 22%; }
        .user-tbl .col-status { width: 22%; }
        .user-tbl .col-action { width: 16%; }
        .user-tbl th, .user-tbl td { font-size: 0.7rem; padding: 0.4rem 0.5rem; }
    }
</style>

<div class="card">
    <div style="padding:16px 20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;border-bottom:1px solid #f3f4f6;">
        <div class="tabs">
            @foreach([
                'all'=>['label'=>'Semua','count'=>$counts['all']],
                'superadmin'=>['label'=>'SuperAdmin','count'=>$counts['superadmin']],
                'admin'=>['label'=>'Admin','count'=>$counts['admin']],
                'owner'=>['label'=>'Owner','count'=>$counts['owner']],
                'buyer'=>['label'=>'Buyer','count'=>$counts['buyer']],
            ] as $key=>$tab)
                <a href="{{ route('users.index', ['role'=>$key,'search'=>$search]) }}"
                   class="tab-item {{ $role===$key?'active':'' }}">
                    {{ $tab['label'] }} <span class="tab-count">{{ $tab['count'] }}</span>
                </a>
            @endforeach
        </div>
        <form method="GET" style="display:flex;gap:8px;">
            <input type="hidden" name="role" value="{{ $role }}" />
            <div class="search-bar" style="width:220px;">
                <span class="search-bar-icon"><i class="fas fa-search"></i></span>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, email, WA..." />
            </div>
            <button type="submit" class="btn btn-secondary btn-sm">Cari</button>
        </form>
    </div>

    <div class="table-wrap">
        @if($users->isEmpty())
            <div class="empty-state"><div class="empty-icon">👥</div><p>Tidak ada pengguna ditemukan.</p></div>
        @else
        <table class="user-tbl">
            <thead>
                <tr>
                    <th class="col-user">Pengguna</th>
                    <th class="col-wa">WhatsApp</th>
                    <th class="col-role">Role</th>
                    <th class="col-status">Status</th>
                    <th class="col-date">Bergabung</th>
                    <th class="col-action">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td class="col-user">
                        <div class="user-name">{{ $user->name }}</div>
                        <div class="user-email">{{ $user->email }}</div>
                    </td>
                    <td class="col-wa">
                        @if($user->whatsapp)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/','',$user->whatsapp) }}" target="_blank"
                               style="color:var(--coffee-400);text-decoration:none;font-size:0.75rem;">{{ $user->whatsapp }}</a>
                        @else
                            <span style="color:#d1d5db;">—</span>
                        @endif
                    </td>
                    <td class="col-role">
                        <span class="badge badge-{{ strtolower($user->role) }}" style="font-size:0.6rem;padding:0.2rem 0.5rem;">{{ $user->role }}</span>
                    </td>
                    <td class="col-status">
                        @if($user->is_verified)
                            <span class="badge badge-approved" style="font-size:0.6rem;padding:0.2rem 0.5rem;">Verified</span>
                        @else
                            <span class="badge badge-pending" style="font-size:0.6rem;padding:0.2rem 0.5rem;">Belum</span>
                        @endif
                    </td>
                    <td class="col-date" style="font-size:0.75rem;color:#9ca3af;">{{ $user->created_at->format('d/m/Y') }}</td>
                    <td class="col-action">
                        <div class="action-flex">
                            @if($user->role === 'OWNER')
                                @if($user->is_verified)
                                    <form method="POST" action="{{ route('users.toggleVerify', $user) }}">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-warning">✗ Unverify</button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('users.toggleVerify', $user) }}">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-success">✓ Verify</button>
                                    </form>
                                @endif
                            @endif
                            <a href="{{ route('users.show', $user) }}" class="btn btn-secondary">Detail</a>
                            @if(auth()->user()->isSuperAdmin() && $user->role !== 'SUPERADMIN')
                                <form method="POST" action="{{ route('users.destroy', $user) }}"
                                      onsubmit="return confirm('Hapus pengguna {{ $user->name }}?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger"><i class="fas fa-trash-alt"></i></button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="pagination-wrap">
            <span>Menampilkan {{ $users->firstItem() }}–{{ $users->lastItem() }} dari {{ $users->total() }} pengguna</span>
            <div class="pagination">
                @if($users->onFirstPage())
                    <span class="page-link disabled">‹</span>
                @else
                    <a href="{{ $users->previousPageUrl() }}" class="page-link">‹</a>
                @endif
                @foreach($users->getUrlRange(max(1,$users->currentPage()-2),min($users->lastPage(),$users->currentPage()+2)) as $page=>$url)
                    <a href="{{ $url }}" class="page-link {{ $page===$users->currentPage()?'active':'' }}">{{ $page }}</a>
                @endforeach
                @if($users->hasMorePages())
                    <a href="{{ $users->nextPageUrl() }}" class="page-link">›</a>
                @else
                    <span class="page-link disabled">›</span>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

@endsection
