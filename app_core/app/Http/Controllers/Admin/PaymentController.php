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
            'status' => 'required|in:pending,paid,failed,refunded',
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

        if ($user && $plan) {
            $store = $user->stores()->first();
            if ($store) {
                $store->update([
                    'membership_plan_id'   => $plan->id,
                    'membership_expires_at' => now()->addDays($plan->duration_days ?? 30),
                ]);
            }
        }

        $payment->update(['status' => 'paid']);

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
