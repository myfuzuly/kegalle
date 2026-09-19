<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WholesalePrice;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class WholesalePriceController extends Controller
{
    public function index(Request $request)
    {
        $date  = $request->get('date', WholesalePrice::latestDate() ?? today()->toDateString());
        $prices = WholesalePrice::where('price_date', $date)
            ->orderBy('category')->orderBy('commodity')->get();

        $dates = WholesalePrice::select('price_date')
            ->groupBy('price_date')->orderByDesc('price_date')->limit(30)
            ->pluck('price_date');

        return view('admin.wholesale-prices.index', compact('prices', 'date', 'dates'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date'             => 'required|date',
            'rows'             => 'required|array|min:1',
            'rows.*.commodity' => 'required|string|max:100',
            'rows.*.category'  => 'required|string|max:60',
            'rows.*.unit'      => 'required|string|max:40',
            'rows.*.min_price' => 'nullable|numeric|min:0',
            'rows.*.max_price' => 'nullable|numeric|min:0',
            'rows.*.avg_price' => 'nullable|numeric|min:0',
        ]);

        $date = Carbon::parse($request->date)->toDateString();
        $now  = now();

        foreach ($request->rows as $row) {
            if (empty(trim($row['commodity']))) continue;

            WholesalePrice::updateOrCreate(
                ['commodity' => $row['commodity'], 'market' => 'Kegalle', 'price_date' => $date],
                [
                    'category'  => $row['category'],
                    'unit'      => $row['unit'],
                    'min_price' => $row['min_price'] ?: null,
                    'max_price' => $row['max_price'] ?: null,
                    'avg_price' => $row['avg_price'] ?: null,
                    'is_active' => true,
                ]
            );
        }

        return redirect()->route('admin.wholesale-prices.index', ['date' => $date])
            ->with('success', 'Prices saved for ' . $date . '.');
    }

    public function destroy(Request $request, string $date)
    {
        WholesalePrice::where('price_date', $date)->delete();
        return redirect()->route('admin.wholesale-prices.index')
            ->with('success', "All prices for {$date} deleted.");
    }
}
