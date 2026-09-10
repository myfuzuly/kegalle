<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;

class AdminReportController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status', 'pending');
        $reports = Report::with(['user', 'listing', 'reviewer'])
            ->when($status !== 'all', fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(30);

        return view('admin.reports.index', compact('reports', 'status'));
    }

    public function update(Request $request, Report $report)
    {
        $request->validate(['status' => 'required|in:reviewed,dismissed,actioned']);

        $report->update([
            'status'      => $request->status,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        if ($request->status === 'actioned' && $report->listing) {
            $report->listing->update(['status' => 'rejected']);
        }

        return back()->with('success', 'Report marked as ' . $request->status . '.');
    }
}
