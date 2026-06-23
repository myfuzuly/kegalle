<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;

class PaymentController extends Controller
{
    public function index()
    {
        return view('admin.payments.index', ['items' => Payment::latest()->take(100)->get()]);
    }
}
