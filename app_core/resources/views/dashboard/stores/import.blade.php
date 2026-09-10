@extends('layouts.store-dashboard')

@section('title', 'Bulk Import — ' . ($store->name ?? 'Store'))
@section('heading', 'Bulk Import Listings')
@section('subheading', 'Upload a CSV file to add multiple listings at once.')

@section('actions')
<a href="/dashboard/stores/{{ $store->id }}" class="kd-btn kd-btn-light">← Back to Store</a>
@endsection

@section('content')
@if(session('success'))
    <div class="kd-alert kd-alert-success alert-green-lg">
        ✅ {{ session('success') }}
    </div>
@endif

<section class="kd-card">
    <div class="kd-card-head"><h2>CSV Format</h2></div>
    <p class="fs14-slate-mb12">Your CSV file must have a header row. Supported columns:</p>
    <div class="overflow-x-auto">
        <table class="table-compact">
            <thead><tr class="bg-slate50">
                <th class="td-padded">Column</th>
                <th class="td-padded">Required</th>
                <th class="td-padded">Example</th>
            </tr></thead>
            <tbody>
                @foreach([
                    ['title','Yes','Blue Samsung Galaxy S23'],
                    ['description','No','Excellent condition, 128GB storage'],
                    ['price','No','45000'],
                    ['location','No','Kegalle'],
                    ['condition','No','Brand New'],
                ] as [$col,$req,$ex])
                <tr>
                    <td class="p8-12-mono-fw6">{{ $col }}</td>
                    <td class="p8-12-border">{{ $req }}</td>
                    <td class="p8-12-border-slate">{{ $ex }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <details class="mt-14">
        <summary class="ptr-green-fw6">📋 Copy example CSV</summary>
        <pre class="code-block">title,description,price,location,condition
"Samsung Galaxy S23","128GB Blue. Excellent condition.",45000,Kegalle,"Brand New"
"Honda Fit 2018","Low mileage, AC working.",3500000,Mawanella,Used
"Wooden Dining Table","6-seater teak wood table.",85000,Kegalle,Used</pre>
    </details>
</section>

<section class="kd-card" class="mt-16">
    <div class="kd-card-head"><h2>Upload CSV</h2></div>
    <form method="POST" action="{{ route('dashboard.stores.import.post', $store->id) }}" enctype="multipart/form-data">
        @csrf
        <div class="mb-16">
            <label class="block-fw6-fs14">Select CSV file <span class="text-danger">*</span></label>
            <input class="upload-area" type="file" name="csv" accept=".csv,.txt" required>
            <p class="fs12-muted-mt6">Max 2 MB · CSV format only · All imported listings will be "pending" until approved by admin</p>
        </div>
        <button type="submit" class="kd-btn kd-btn-primary min-w-160">📥 Import Listings</button>
    </form>
</section>
@endsection
