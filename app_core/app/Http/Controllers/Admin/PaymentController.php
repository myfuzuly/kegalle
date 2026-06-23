<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

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

    public function destroy(Payment $payment)
    {
        $payment->delete();

        return back()->with('success', 'Payment record deleted.');
    }
}
