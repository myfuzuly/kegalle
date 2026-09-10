<!doctype html>
<html>
<head><meta charset="utf-8"><title>Price Drop Alert — kegalle</title></head>
<body style="margin:0;background:#f5f7fb;font-family:Arial,sans-serif;color:#0f172a;">
<div style="max-width:620px;margin:40px auto;background:#fff;border-radius:16px;padding:34px;">
    <p style="margin:0 0 20px;font-size:13px;color:#667085;">kegalle · Kegalle Marketplace</p>
    <h1 style="margin:0 0 6px;font-size:22px;">🔥 Price dropped on your saved items</h1>
    <p style="color:#667085;margin-top:4px">Good news — {{ $drops->count() }} {{ $drops->count() === 1 ? 'item' : 'items' }} you saved got cheaper.</p>

    @foreach($drops as $row)
    @php
        $drop = $row->old_price - $row->new_price;
        $pct  = $row->old_price > 0 ? round($drop / $row->old_price * 100) : 0;
    @endphp
    <div style="border:1px solid #e2e8f0;border-radius:12px;padding:16px;margin:14px 0;display:flex;gap:14px">
        <div style="flex:1">
            <p style="margin:0 0 4px;font-weight:bold;font-size:15px">{{ $row->title }}</p>
            <p style="margin:0;color:#64748b;font-size:13px">{{ $row->category ?? '' }}</p>
            <p style="margin:8px 0 0">
                <span style="text-decoration:line-through;color:#94a3b8;font-size:13px">LKR {{ number_format($row->old_price) }}</span>
                &nbsp;→&nbsp;
                <strong style="color:#00A76F;font-size:17px">LKR {{ number_format($row->new_price) }}</strong>
                <span style="background:#dcfce7;color:#15803d;border-radius:20px;padding:2px 8px;font-size:12px;margin-left:8px">−{{ $pct }}%</span>
            </p>
        </div>
    </div>
    <p style="margin:4px 0 14px;text-align:right">
        <a href="{{ url('/listings/'.($row->slug ?? '')) }}"
           style="display:inline-block;background:#00A76F;color:#fff;padding:10px 18px;border-radius:8px;text-decoration:none;font-size:13px;font-weight:bold">
            View Item →
        </a>
    </p>
    @endforeach

    <p style="margin-top:24px">
        <a href="{{ url('/dashboard/favorites') }}"
           style="display:inline-block;background:#0f172a;color:#fff;padding:13px 20px;border-radius:10px;text-decoration:none;font-weight:bold;">
            View All Saved Items
        </a>
    </p>
    <p style="color:#94a3b8;font-size:12px;margin-top:24px;">You saved these items on kegalle. <a href="{{ url('/dashboard/favorites') }}" style="color:#94a3b8">Manage saved items</a>.</p>
</div>
</body>
</html>
