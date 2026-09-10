@forelse($notifications as $notif)
@php
    $icons = ['offer_received'=>'📬','offer_accepted'=>'✅','offer_rejected'=>'❌','payment_success'=>'💳','listing_approved'=>'🎉','listing_rejected'=>'📋'];
    $icon    = $icons[$notif->type] ?? '🔔';
    $isUnread = !$notif->read_at;
@endphp
<div class="ntf-row {{ $isUnread ? 'ntf-row-unread' : '' }} csp5-286" data-title="{{ strtolower($notif->title ?? '') }}">
    <div class="{{ $isUnread ? 'ntf-unread-bar' : 'ntf-read-bar' }} mr-14"></div>
    <div class="ntf-icon">{{ $icon }}</div>
    <div class="ntf-body ml-10">
        <div class="ntf-title {{ $isUnread ? 'ntf-title-unread' : 'ntf-title-read' }}">{{ $notif->title }}</div>
        @if($notif->body)<p class="ntf-text">{{ $notif->body }}</p>@endif
        <div class="ntf-time">{{ $notif->created_at->diffForHumans() }}</div>
    </div>
    <div class="ntf-right">
        @if($notif->url)
        <a href="{{ $notif->url }}" class="ntf-view-link">View →</a>
        @endif
        @if($isUnread)
        <form class="inline-m0" method="POST" action="/dashboard/notifications/{{ $notif->id }}/read">
            @csrf
            <button type="submit" class="ntf-read-btn" title="Mark as read">✓</button>
        </form>
        @endif
    </div>
</div>
@empty
<div class="ntf-empty">
    <span class="emoji-48">🔔</span>
    <strong class="heading-sm">No notifications</strong>
    <p class="desc-text">Nothing here yet.</p>
</div>
@endforelse
