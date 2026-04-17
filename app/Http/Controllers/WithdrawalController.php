<?php

namespace App\Http\Controllers;

use App\Models\Withdrawal;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $search = $request->get('search', '');

        $query = Withdrawal::with('owner');

        if ($status !== 'all') {
            $query->where('status', strtoupper($status));
        }

        if ($search) {
            $query->whereHas('owner', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhere('bank_name', 'like', "%{$search}%")
              ->orWhere('bank_account', 'like', "%{$search}%");
        }

        $withdrawals = $query->latest()->paginate(15)->withQueryString();

        $counts = [
            'all'      => Withdrawal::count(),
            'pending'  => Withdrawal::where('status', 'PENDING')->count(),
            'approved' => Withdrawal::where('status', 'APPROVED')->count(),
            'rejected' => Withdrawal::where('status', 'REJECTED')->count(),
        ];

        return view('withdrawals.index', compact('withdrawals', 'status', 'search', 'counts'));
    }

    public function approve(Withdrawal $withdrawal)
    {
        if ($withdrawal->status !== 'PENDING') {
            return back()->with('error', 'Hanya withdrawal dengan status PENDING yang bisa disetujui.');
        }
        $withdrawal->update(['status' => 'APPROVED']);
        return back()->with('success', "Withdrawal #{$withdrawal->id} berhasil disetujui.");
    }

    public function reject(Withdrawal $withdrawal)
    {
        if ($withdrawal->status !== 'PENDING') {
            return back()->with('error', 'Hanya withdrawal dengan status PENDING yang bisa ditolak.');
        }
        $withdrawal->update(['status' => 'REJECTED']);
        return back()->with('success', "Withdrawal #{$withdrawal->id} telah ditolak.");
    }
}
