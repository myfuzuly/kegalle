<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\MembershipPlan;
use App\Models\Payment;
use Illuminate\Http\Request;

class MembershipUpgradeController extends Controller
{
    // Matches the hardcoded plans in memberships/index.blade.php
    private array $plans = [
        'silver'   => ['name' => 'Silver',   'price' => 990,  'duration_days' => 30],
        'gold'     => ['name' => 'Gold',     'price' => 2490, 'duration_days' => 30],
        'platinum' => ['name' => 'Platinum', 'price' => 4990, 'duration_days' => 30],
    ];

    public function index()
    {
        $user  = auth()->user();
        $store = $user->stores()->with('membershipPlan')->first();

        // Current active plan slug (null = free)
        $currentSlug = null;
        $expiresAt   = null;
        if ($store && $store->membership_plan_id && $store->membership_expires_at?->isFuture()) {
            $currentSlug = $store->membershipPlan?->slug;
            $expiresAt   = $store->membership_expires_at;
        }

        // Plans with pending payments
        $pendingSlugs = Payment::where('user_id', $user->id)
            ->where('status', 'pending')
            ->get()
            ->pluck('meta.plan_slug')
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        return view('dashboard.memberships.index', compact('currentSlug', 'expiresAt', 'pendingSlugs'));
    }

    public function payments()
    {
        $payments = Payment::where('user_id', auth()->id())
            ->with('membershipPlan')
            ->latest()
            ->paginate(15);
        return view('dashboard.payments.index', compact('payments'));
    }

    public function show(string $plan)
    {
        abort_unless(array_key_exists($plan, $this->plans), 404);

        $meta      = $this->plans[$plan];
        $planModel = MembershipPlan::where('slug', $plan)->first();
        $reference = 'KRL-' . auth()->id() . '-' . strtoupper(substr($plan, 0, 3)) . '-' . now()->format('Ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(4));

        // Block if already pending
        $pending = Payment::where('user_id', auth()->id())
            ->where('status', 'pending')
            ->whereJsonContains('meta->plan_slug', $plan)
            ->exists();

        return view('dashboard.memberships.upgrade', compact('plan', 'planModel', 'meta', 'reference', 'pending'));
    }

    public function submit(Request $request, string $plan)
    {
        abort_unless(array_key_exists($plan, $this->plans), 404);

        $request->validate([
            'slip'      => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'confirmed' => 'required|accepted',
        ], [
            'slip.required'      => 'Please upload your bank transfer slip or screenshot.',
            'slip.mimes'         => 'Slip must be a JPG, PNG, or PDF file.',
            'slip.max'           => 'Slip file must be under 5MB.',
            'confirmed.accepted' => 'Please confirm that you have made the bank transfer.',
        ]);

        $meta      = $this->plans[$plan];
        $reference = 'KRL-' . auth()->id() . '-' . strtoupper(substr($plan, 0, 3)) . '-' . now()->format('Ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(4));
        $planModel = MembershipPlan::where('slug', $plan)->first();
        $slipPath  = $request->file('slip')->store('payment-slips', 'local');

        Payment::create([
            'user_id'            => auth()->id(),
            'membership_plan_id' => $planModel?->id,
            'gateway'            => 'offline',
            'reference'          => $reference,
            'amount'             => $meta['price'],
            'currency'           => 'LKR',
            'status'             => 'pending',
            'meta'               => [
                'slip'      => $slipPath,
                'plan_slug' => $plan,
                'plan_name' => $meta['name'],
            ],
        ]);

        return redirect('/dashboard/membership')
            ->with('success', 'Payment slip received! Your ' . $meta['name'] . ' plan will be activated within 24 hours after we verify your transfer.');
    }
}
