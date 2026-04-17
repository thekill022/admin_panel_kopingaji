<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $search = $request->get('search', '');

        $query = Order::with(['buyer', 'umkm', 'payment']);

        if ($status !== 'all') {
            $query->where('status', strtoupper($status));
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('buyer', fn($b) => $b->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('umkm', fn($u) => $u->where('name', 'like', "%{$search}%"))
                  ->orWhere('id', $search);
            });
        }

        $orders = $query->latest()->paginate(15)->withQueryString();

        $counts = [
            'all'       => Order::count(),
            'pending'   => Order::where('status', 'PENDING')->count(),
            'paid'      => Order::where('status', 'PAID')->count(),
            'completed' => Order::where('status', 'COMPLETED')->count(),
            'cancelled' => Order::where('status', 'CANCELLED')->count(),
        ];

        return view('orders.index', compact('orders', 'status', 'search', 'counts'));
    }

    public function show(Order $order)
    {
        $order->load(['buyer', 'umkm', 'items.product', 'payment']);
        return view('orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => ['required', 'in:PENDING,PAID,COMPLETED,CANCELLED'],
        ]);

        $order->update(['status' => $request->status]);
        return back()->with('success', "Status order #{$order->id} diubah ke {$request->status}.");
    }
}
