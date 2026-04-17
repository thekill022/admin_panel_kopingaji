<?php

namespace App\Http\Controllers;

use App\Models\Refund;
use Illuminate\Http\Request;

class RefundController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $search = $request->get('search', '');

        $query = Refund::with(['order.buyer', 'order.umkm']);

        if ($status !== 'all') {
            $query->where('status', strtoupper($status));
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('order.buyer', function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhereHas('order.umkm', function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%");
                })
                ->orWhere('reason', 'like', "%{$search}%");
            });
        }

        $refunds = $query->latest()->paginate(15)->withQueryString();

        $counts = [
            'all'      => Refund::count(),
            'pending'  => Refund::where('status', 'PENDING')->count(),
            'approved' => Refund::where('status', 'APPROVED')->count(),
            'rejected' => Refund::where('status', 'REJECTED')->count(),
        ];

        return view('refunds.index', compact('refunds', 'status', 'search', 'counts'));
    }

    public function show(Refund $refund)
    {
        $refund->load(['order.buyer', 'order.umkm', 'order.items.product', 'order.payment']);

        return view('refunds.show', compact('refund'));
    }

    public function approve(Refund $refund)
    {
        if ($refund->status !== 'PENDING') {
            return back()->with('error', 'Hanya pengajuan refund dengan status PENDING yang bisa disetujui.');
        }

        $refund->update([
            'status'      => 'APPROVED',
            'refunded_at' => now(),
        ]);

        // Update order status to REFUNDED if applicable
        if ($refund->order && in_array($refund->order->status, ['PAID', 'COMPLETED'])) {
            $refund->order->update(['status' => 'REFUNDED']);
        }

        return back()->with('success', "Refund #{$refund->id} berhasil disetujui. Dana akan dikembalikan ke pembeli.");
    }

    public function reject(Refund $refund)
    {
        if ($refund->status !== 'PENDING') {
            return back()->with('error', 'Hanya pengajuan refund dengan status PENDING yang bisa ditolak.');
        }

        $refund->update(['status' => 'REJECTED']);

        return back()->with('success', "Refund #{$refund->id} telah ditolak.");
    }
}
