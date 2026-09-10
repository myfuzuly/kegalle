@forelse($brands as $brand)
<tr>
    <td>
        <div class="kaa-name-cell">
            <span class="kaa-name-primary">{{ $brand->name }}</span>
            <span class="kaa-name-sub">#{{ $brand->id }}</span>
        </div>
    </td>
    <td>
        @if($brand->categories->isNotEmpty())
        <div class="flex-fw-g4">
            @foreach($brand->categories as $cat)
            <span class="chip-green">
                {{ $cat->name }}
            </span>
            @endforeach
        </div>
        @else
        <span class="slate300-fs12">—</span>
        @endif
    </td>
    <td><span class="sa-status {{ $brand->is_active ? 'active' : 'suspended' }}">{{ $brand->is_active ? 'Active' : 'Inactive' }}</span></td>
    <td class="kaa-num kaa-num-muted">{{ $brand->sort_order }}</td>
    <td>
        <div class="kaa-actions">
            <a href="/admin/brands/{{ $brand->id }}/edit" class="kaa-act kaa-act-edit">Edit</a>
            <a href="/brand/{{ $brand->slug }}" target="_blank" class="kaa-act kaa-act-view">View</a>
            <form method="post" action="/admin/brands/{{ $brand->id }}/toggle" class="d-contents">@csrf
                <button class="kaa-act kaa-act-toggle">{{ $brand->is_active ? 'Deactivate' : 'Activate' }}</button>
            </form>
            <form method="post" action="/admin/brands/{{ $brand->id }}" class="d-contents" onsubmit="return confirm('Delete {{ addslashes($brand->name) }} and all its models?')">@csrf @method('DELETE')
                <button class="kaa-act kaa-act-del">Delete</button>
            </form>
        </div>
    </td>
</tr>
@empty
<tr><td colspan="5">
    <div class="kaa-empty">
        <span class="kaa-empty-icon">🏷</span>
        <span class="kaa-empty-text">No brands match your search</span>
    </div>
</td></tr>
@endforelse
