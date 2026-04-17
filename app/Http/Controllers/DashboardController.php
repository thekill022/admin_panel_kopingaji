<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Umkm;
use App\Models\User;
use App\Models\Withdrawal;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users'       => User::count(),
            'total_owners'      => User::where('role', 'OWNER')->count(),
            'total_buyers'      => User::where('role', 'BUYER')->count(),
            'total_admins'      => User::whereIn('role', ['ADMIN'])->count(),
            'total_umkms'       => Umkm::count(),
            'verified_umkms'    => Umkm::where('is_verified', true)->count(),
            'unverified_umkms'  => Umkm::where('is_verified', false)->count(),
            'total_products'    => Product::count(),
            'pending_products'  => Product::where('status', 'PENDING')->count(),
            'approved_products' => Product::where('status', 'APPROVED')->count(),
            'rejected_products' => Product::where('status', 'REJECTED')->count(),
            'total_orders'      => Order::count(),
            'pending_orders'    => Order::where('status', 'PENDING')->count(),
            'completed_orders'  => Order::where('status', 'COMPLETED')->count(),
            'total_revenue'     => Order::where('status', 'COMPLETED')->sum('total_price'),
            'pending_withdrawals' => Withdrawal::where('status', 'PENDING')->count(),
            'unverified_users'  => User::where('is_verified', false)->where('role', 'OWNER')->count(),
        ];

        $recent_orders      = Order::with(['buyer', 'umkm'])->latest()->take(5)->get();
        $pending_products   = Product::with('umkm')->where('status', 'PENDING')->latest()->take(5)->get();
        $pending_withdrawals = Withdrawal::with('owner')->where('status', 'PENDING')->latest()->take(5)->get();

        return view('dashboard', compact('stats', 'recent_orders', 'pending_products', 'pending_withdrawals'));
    }
}
