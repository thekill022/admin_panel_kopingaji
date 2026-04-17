<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminAccountController extends Controller
{
    /**
     * Tampilkan daftar semua admin (hanya SUPERADMIN bisa akses)
     */
    public function index()
    {
        $admins = User::where('role', 'ADMIN')->latest()->paginate(15);
        return view('admin-accounts.index', compact('admins'));
    }

    /**
     * Form buat akun admin baru
     */
    public function create()
    {
        return view('admin-accounts.create');
    }

    /**
     * Simpan akun admin baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', 'unique:users,email'],
            'password'  => ['required', 'string', 'min:8', 'confirmed'],
            'whatsapp'  => ['nullable', 'string', 'max:20'],
        ]);

        User::create([
            'name'        => $validated['name'],
            'email'       => $validated['email'],
            'password'    => Hash::make($validated['password']),
            'role'        => 'ADMIN',
            'whatsapp'    => $validated['whatsapp'] ?? null,
            'is_verified' => true,
        ]);

        return redirect()->route('admin-accounts.index')
                         ->with('success', "Akun admin \"{$validated['name']}\" berhasil dibuat.");
    }

    /**
     * Form edit akun admin
     */
    public function edit(User $adminAccount)
    {
        if ($adminAccount->role !== 'ADMIN') {
            abort(404);
        }
        return view('admin-accounts.edit', ['admin' => $adminAccount]);
    }

    /**
     * Update akun admin
     */
    public function update(Request $request, User $adminAccount)
    {
        if ($adminAccount->role !== 'ADMIN') {
            abort(404);
        }

        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', Rule::unique('users')->ignore($adminAccount->id)],
            'password'  => ['nullable', 'string', 'min:8', 'confirmed'],
            'whatsapp'  => ['nullable', 'string', 'max:20'],
        ]);

        $data = [
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'whatsapp' => $validated['whatsapp'] ?? null,
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $adminAccount->update($data);

        return redirect()->route('admin-accounts.index')
                         ->with('success', "Akun admin \"{$adminAccount->name}\" berhasil diperbarui.");
    }

    /**
     * Nonaktifkan / Hapus akun admin
     */
    public function destroy(User $adminAccount)
    {
        if ($adminAccount->role !== 'ADMIN') {
            abort(404);
        }
        $name = $adminAccount->name;
        $adminAccount->delete();
        return redirect()->route('admin-accounts.index')
                         ->with('success', "Akun admin \"{$name}\" berhasil dihapus.");
    }
}
