<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MembershipPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MembershipController extends Controller
{
    public function index()
    {
        return view('admin.memberships.index', ['plans' => MembershipPlan::all()]);
    }

    public function store(Request $r)
    {
        $d = $r->validate(['name' => 'required', 'price' => 'required|numeric', 'duration_days' => 'required|integer', 'ad_limit' => 'required|integer', 'product_limit' => 'required|integer']);
        $d['slug'] = Str::slug($d['name']);
        $d['is_active'] = true;
        MembershipPlan::create($d);

        return back();
    }
}
