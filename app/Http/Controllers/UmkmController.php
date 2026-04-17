<?php

namespace App\Http\Controllers;

use App\Models\Umkm;
use Illuminate\Http\Request;

class UmkmController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');

        $query = Umkm::with('owner');

        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhereHas('owner', fn($q) => $q->where('name', 'like', "%{$search}%"));
        }

        $umkms = $query->latest()->paginate(15)->withQueryString();

        return view('umkms.index', compact('umkms', 'search'));
    }

    public function show(Umkm $umkm)
    {
        $umkm->load(['owner', 'products', 'orders']);
        $stats = [
            'total_products'  => $umkm->products->count(),
            'pending_products' => $umkm->products->where('status', 'PENDING')->count(),
            'approved_products' => $umkm->products->where('status', 'APPROVED')->count(),
            'total_orders'    => $umkm->orders->count(),
            'total_revenue'   => $umkm->orders->where('status', 'COMPLETED')->sum('total_price'),
        ];
        return view('umkms.show', compact('umkm', 'stats'));
    }

    public function updatePlatformFee(Request $request, Umkm $umkm)
    {
        $request->validate([
            'platform_fee_type'    => ['required', 'in:percentage,flat'],
            'platform_fee_rate'    => ['required_if:platform_fee_type,percentage', 'nullable', 'numeric', 'min:0', 'max:100'],
            'platform_fee_flat'    => ['required_if:platform_fee_type,flat', 'nullable', 'numeric', 'min:0'],
            'tax_threshold'        => ['nullable', 'numeric', 'min:0'],
            'tax_rate'             => ['nullable', 'numeric', 'min:0', 'max:100'],
            'withdrawal_admin_fee' => ['nullable', 'numeric', 'min:0'],
        ]);

        $umkm->update([
            'platform_fee_type'    => $request->platform_fee_type,
            'platform_fee_rate'    => $request->platform_fee_rate ?? 0,
            'platform_fee_flat'    => $request->platform_fee_flat ?? 0,
            'tax_threshold'        => $request->tax_threshold ?? 0,
            'tax_rate'             => $request->tax_rate ?? 0,
            'withdrawal_admin_fee' => $request->withdrawal_admin_fee ?? 0,
        ]);

        return back()->with('success', "Pengaturan fee UMKM \"{$umkm->name}\" berhasil diperbarui.");
    }

    public function toggleVerify(Umkm $umkm)
    {
        $umkm->update(['is_verified' => ! $umkm->is_verified]);
        $status = $umkm->is_verified ? 'diverifikasi' : 'dibatalkan verifikasinya';
        return back()->with('success', "UMKM \"{$umkm->name}\" berhasil $status.");
    }

    public function destroy(Umkm $umkm)
    {
        $name = $umkm->name;
        $umkm->delete();
        return redirect()->route('umkms.index')->with('success', "UMKM \"{$name}\" berhasil dihapus.");
    }
}
