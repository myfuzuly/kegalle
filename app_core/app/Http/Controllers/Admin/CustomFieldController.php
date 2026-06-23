<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CustomField;
use App\Models\CustomFieldGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CustomFieldController extends Controller
{
    public function index()
    {
        return view('admin.fields.index', ['fields' => CustomField::latest()->get(), 'groups' => CustomFieldGroup::all(), 'categories' => Category::all()]);
    }

    public function store(Request $r)
    {
        $d = $r->validate(['label' => 'required', 'type' => 'required', 'group_id' => 'nullable']);
        $d['name'] = Str::snake($d['label']);
        if ($r->options) {
            $d['options'] = array_map('trim', explode(',', $r->options));
        } CustomField::create($d);

        return back();
    }
}
