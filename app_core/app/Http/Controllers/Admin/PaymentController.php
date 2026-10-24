<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MembershipPlan;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with(['user', 'membershipPlan'])->latest()->paginate(30);

        return view('admin.payments.index', compact('payments'));
    }

    public function updateStatus(Request $request, Payment $payment)
    {
        $data = $request->validate([
            'status' => 'required|in:pending,failed,refunded', // "paid" only via approve(), which also activates the plan
        ]);

        $payment->update($data);

        return back()->with('success', 'Payment status updated.');
    }

    public function approve(Payment $payment)
    {
        if ($payment->status === 'paid') {
            return back()->with('success', 'Already approved.');
        }

        $user = $payment->user;
        $plan = $payment->membershipPlan
            ?? MembershipPlan::where('slug', $payment->meta['plan_slug'] ?? '')->first();

        \Illuminate\Support\Facades\DB::transaction(function () use ($user, $plan, $payment) {
            $store = $user && $plan ? $user->stores()->first() : null;
            if ($store) {
                // Renewing early adds to the time already paid for instead of wiping it
                $from = $store->membership_expires_at && $store->membership_expires_at->isFuture()
                    ? $store->membership_expires_at->copy()
                    : now();
                // forceFill: these columns are deliberately not mass-assignable, so update() silently dropped them
                $store->forceFill([
                    'membership_plan_id'    => $plan->id,
                    'membership_expires_at' => $from->addDays($plan->duration_days ?? 30),
                ])->save();
            }
            $payment->update(['status' => 'paid']);
        });

        return back()->with('success', 'Payment approved — ' . ($plan->name ?? 'plan') . ' activated for ' . ($user->name ?? 'user') . '.');
    }

    public function reject(Request $request, Payment $payment)
    {
        $payment->update(['status' => 'failed']);
        return back()->with('success', 'Payment rejected.');
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();

        return back()->with('success', 'Payment record deleted.');
    }

    public function slip(Payment $payment)
    {
        $path = $payment->meta['slip'] ?? null;
        abort_unless($path && Storage::disk('local')->exists($path), 404);

        $mime = Storage::disk('local')->mimeType($path);
        return response()->stream(function () use ($path) {
            echo Storage::disk('local')->get($path);
        }, 200, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="slip-' . $payment->id . '.' . pathinfo($path, PATHINFO_EXTENSION) . '"',
        ]);
    }
}
