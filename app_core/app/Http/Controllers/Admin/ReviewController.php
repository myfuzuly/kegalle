<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;

class ReviewController extends Controller
{
    public function index()
    {
        return view('admin.reviews.index', ['items' => Review::latest()->take(100)->get()]);
    }
}
