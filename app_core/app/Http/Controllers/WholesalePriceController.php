<?php

namespace App\Http\Controllers;

use App\Models\WholesalePrice;
use Illuminate\Support\Carbon;

class WholesalePriceController extends Controller
{
    public function index()
    {
        $latestDate = WholesalePrice::latestDate();
        $prices     = $latestDate ? WholesalePrice::forDate($latestDate) : collect();
        $grouped    = $prices->groupBy('category');
        $updatedAt  = $latestDate
            ? Carbon::parse($latestDate)->translatedFormat('F j, Y')
            : null;

        return view('pages.market-prices', compact('grouped', 'updatedAt', 'latestDate'));
    }
}
