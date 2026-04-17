<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $search = $request->get('search', '');

        $query = Report::with(['reporter', 'umkm', 'product'])->latest();

        if ($status !== 'all') {
            $query->where('status', strtoupper($status));
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('reporter', fn($r) => $r->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('umkm', fn($u) => $u->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('product', fn($p) => $p->where('name', 'like', "%{$search}%"))
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $reports = $query->paginate(15)->withQueryString();

        $counts = [
            'all'      => Report::count(),
            'pending'  => Report::where('status', 'PENDING')->count(),
            'reviewed' => Report::where('status', 'REVIEWED')->count(),
            'dismissed' => Report::where('status', 'DISMISSED')->count(),
        ];

        return view('reports.index', compact('reports', 'status', 'search', 'counts'));
    }

    public function show(Report $report)
    {
        $report->load(['reporter', 'umkm', 'product.umkm']);
        $categories = Report::$categories;
        return view('reports.show', compact('report', 'categories'));
    }

    public function review(Request $request, Report $report)
    {
        $request->validate([
            'admin_note' => 'nullable|string|max:1000',
        ]);

        $report->update([
            'status'     => 'REVIEWED',
            'admin_note' => $request->admin_note,
        ]);

        return back()->with('success', "Laporan #{$report->id} telah ditandai sebagai Reviewed.");
    }

    public function dismiss(Request $request, Report $report)
    {
        $request->validate([
            'admin_note' => 'nullable|string|max:1000',
        ]);

        $report->update([
            'status'     => 'DISMISSED',
            'admin_note' => $request->admin_note,
        ]);

        return back()->with('success', "Laporan #{$report->id} telah di-dismiss.");
    }

    public function destroy(Report $report)
    {
        $report->delete();
        return redirect()->route('reports.index')->with('success', 'Laporan berhasil dihapus.');
    }
}
