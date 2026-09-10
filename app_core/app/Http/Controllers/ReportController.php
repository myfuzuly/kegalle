<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function store(Request $request, $listingId)
    {
        $request->validate([
            'reason'  => 'required|string|max:100',
            'details' => 'nullable|string|max:1000',
        ]);

        $existing = Report::where('user_id', auth()->id())
            ->where('listing_id', $listingId)
            ->first();

        if ($existing) {
            return response()->json(['message' => 'You have already reported this listing.'], 422);
        }

        Report::create([
            'user_id'    => auth()->id(),
            'listing_id' => $listingId,
            'reason'     => $request->reason,
            'details'    => $request->details,
        ]);

        return response()->json(['message' => 'Report submitted. Thank you.']);
    }
}
