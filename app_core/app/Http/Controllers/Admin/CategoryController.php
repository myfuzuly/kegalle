<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        return view('admin.categories.index', ['categories' => Category::orderBy('sort_order')->get()]);
    }

    public function store(Request $r)
    {
        $d = $r->validate(['name' => 'required', 'type' => 'required']);
        $d['slug'] = Str::slug($d['name']);
        $d['is_active'] = true;
        Category::create($d);

        return back();
    }
}
