<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $search = $request->get('search', '');

        $query = Product::with(['umkm.owner']);

        if ($status !== 'all') {
            $query->where('status', strtoupper($status));
        }

        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhereHas('umkm', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
        }

        $products = $query->latest()->paginate(15)->withQueryString();

        $counts = [
            'all'      => Product::count(),
            'pending'  => Product::where('status', 'PENDING')->count(),
            'approved' => Product::where('status', 'APPROVED')->count(),
            'rejected' => Product::where('status', 'REJECTED')->count(),
        ];

        return view('products.index', compact('products', 'status', 'search', 'counts'));
    }

    public function show(Product $product)
    {
        $product->load(['umkm.owner', 'orderItems']);
        return view('products.show', compact('product'));
    }

    public function approve(Product $product)
    {
        $product->update(['status' => 'APPROVED']);
        return back()->with('success', "Produk \"{$product->name}\" berhasil disetujui.");
    }

    public function reject(Request $request, Product $product)
    {
        $product->update(['status' => 'REJECTED']);
        return back()->with('success', "Produk \"{$product->name}\" telah ditolak.");
    }

    public function setPending(Product $product)
    {
        $product->update(['status' => 'PENDING']);
        return back()->with('success', "Produk \"{$product->name}\" dikembalikan ke status PENDING.");
    }

    public function destroy(Product $product)
    {
        $name = $product->name;
        $product->delete();
        return redirect()->route('products.index')->with('success', "Produk \"{$name}\" berhasil dihapus.");
    }
}
