<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $role   = $request->get('role', 'all');
        $search = $request->get('search', '');

        $query = User::query();

        if ($role !== 'all') {
            $query->where('role', strtoupper($role));
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('whatsapp', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        $counts = [
            'all'        => User::count(),
            'superadmin' => User::where('role', 'SUPERADMIN')->count(),
            'admin'      => User::where('role', 'ADMIN')->count(),
            'owner'      => User::where('role', 'OWNER')->count(),
            'buyer'      => User::where('role', 'BUYER')->count(),
        ];

        return view('users.index', compact('users', 'role', 'search', 'counts'));
    }

    public function show(User $user)
    {
        $user->load(['umkms.products', 'orders', 'withdrawals']);
        return view('users.show', compact('user'));
    }

    public function toggleVerify(User $user)
    {
        $newStatus = ! $user->is_verified;
        $user->update(['is_verified' => $newStatus]);
        $status = $newStatus ? 'diverifikasi' : 'dibatalkan verifikasinya';

        // If user is being unverified, also unverify all their UMKMs
        if (! $newStatus && $user->isOwner()) {
            $unverifiedCount = $user->umkms()->where('is_verified', true)->update(['is_verified' => false]);
            if ($unverifiedCount > 0) {
                return back()->with('success', "Pengguna \"{$user->name}\" berhasil {$status}. {$unverifiedCount} UMKM miliknya juga otomatis dibatalkan verifikasinya karena pengguna tidak diverifikasi.");
            }
        }

        return back()->with('success', "Pengguna \"{$user->name}\" berhasil {$status}.");
    }

    public function destroy(User $user)
    {
        if ($user->role === 'SUPERADMIN') {
            return back()->with('error', 'Tidak dapat menghapus SuperAdmin.');
        }
        $name = $user->name;
        $user->delete();
        return redirect()->route('users.index')->with('success', "Pengguna \"{$name}\" berhasil dihapus.");
    }
}
