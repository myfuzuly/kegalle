<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MembershipPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class MembershipController extends Controller
{
    public function index()
    {
        $plans = MembershipPlan::orderBy('price')->get();

        return view('admin.memberships.index', compact('plans'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'ad_limit' => 'required|integer|min:0',
            'product_limit' => 'required|integer|min:0',
            'store_limit' => 'nullable|integer|min:0',
            'featured_quota' => 'nullable|integer|min:0',
        ]);

        MembershipPlan::create([
            ...$data,
            'slug' => Str::slug($data['name']).'-'.Str::random(4),
            'store_limit' => $data['store_limit'] ?? 1,
            'featured_quota' => $data['featured_quota'] ?? 0,
            'is_active' => true,
        ]);

        return back()->with('success', 'Membership plan created successfully.');
    }

    public function edit(MembershipPlan $membership)
    {
        return view('admin.memberships.edit', ['plan' => $membership]);
    }

    public function update(Request $request, MembershipPlan $membership)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'slug' => ['required', 'string', 'max:150', Rule::unique('membership_plans', 'slug')->ignore($membership->id)],
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'ad_limit' => 'required|integer|min:0',
            'product_limit' => 'required|integer|min:0',
            'store_limit' => 'nullable|integer|min:0',
            'featured_quota' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $membership->update([
            ...$data,
            'slug' => Str::slug($data['slug']),
            'store_limit' => $data['store_limit'] ?? 1,
            'featured_quota' => $data['featured_quota'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect('/admin/memberships')->with('success', 'Membership plan updated successfully.');
    }

    public function toggle(MembershipPlan $membership)
    {
        $membership->is_active = ! (bool) $membership->is_active;
        $membership->save();

        return back()->with('success', 'Membership plan status updated.');
    }

    public function destroy(MembershipPlan $membership)
    {
        $membership->delete();

        return back()->with('success', 'Membership plan deleted successfully.');
    }
}
