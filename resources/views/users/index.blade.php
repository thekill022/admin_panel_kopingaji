@extends('layouts.app')

@section('title', 'Manajemen Pengguna')
@section('page-title', 'Manajemen Pengguna')
@section('page-subtitle', 'Kelola semua pengguna platform')

@section('content')

<div class="card">
    <div style="padding:16px 20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;border-bottom:1px solid var(--dark-600);">
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
            <div class="search-bar" style="width:260px;">
                <span class="search-bar-icon">🔍</span>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, email, WA..." />
            </div>
            <button type="submit" class="btn btn-secondary btn-sm">Cari</button>
        </form>
    </div>

    <div class="table-wrap">
        @if($users->isEmpty())
            <div class="empty-state"><div class="empty-icon">👥</div><p>Tidak ada pengguna ditemukan.</p></div>
        @else
        <table>
            <thead>
                <tr>
                    <th>Pengguna</th>
                    <th>Email</th>
                    <th>WhatsApp</th>
                    <th>Role</th>
                    <th>Verified</th>
                    <th>Bergabung</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div class="avatar-sm">{{ strtoupper(substr($user->name,0,1)) }}</div>
                            <div class="td-primary">{{ $user->name }}</div>
                        </div>
                    </td>
                    <td class="td-primary">{{ $user->email }}</td>
                    <td>
                        @if($user->whatsapp)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/','',$user->whatsapp) }}" target="_blank"
                               style="color:var(--coffee-400);text-decoration:none;">{{ $user->whatsapp }}</a>
                        @else
                            <span class="td-muted">—</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge badge-{{ strtolower($user->role) }}">{{ $user->role }}</span>
                    </td>
                    <td>
                        @if($user->is_verified)
                            <span class="badge badge-approved">✓ Terverifikasi</span>
                        @else
                            <span class="badge badge-pending">Belum</span>
                        @endif
                    </td>
                    <td class="td-muted">{{ $user->created_at->format('d/m/Y') }}</td>
                    <td>
                        <div style="display:flex;gap:6px;flex-wrap:wrap;">
                            <a href="{{ route('users.show', $user) }}" class="btn btn-xs btn-secondary">Detail</a>
                            @if($user->role === 'OWNER')
                                <form method="POST" action="{{ route('users.toggleVerify', $user) }}">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-xs {{ $user->is_verified ? 'btn-warning' : 'btn-success' }}">
                                        {{ $user->is_verified ? '✗ Unverify' : '✓ Verify' }}
                                    </button>
                                </form>
                            @endif
                            @if(auth()->user()->isSuperAdmin() && $user->role !== 'SUPERADMIN')
                                <form method="POST" action="{{ route('users.destroy', $user) }}"
                                      onsubmit="return confirm('Hapus pengguna {{ $user->name }}?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-xs btn-danger">🗑</button>
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
