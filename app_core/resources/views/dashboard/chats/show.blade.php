@extends('layouts.dashboard')

@section('title', 'Chat — ' . ($otherUser->name ?? 'User'))
@section('eyebrow', 'Messages')
@section('heading', $otherUser->name ?? 'User')
@section('subheading')
    Re: <a class="text-kd-primary" href="/listings/{{ $thread->listing->slug ?? $thread->listing_id }}">{{ optional($thread->listing)->title ?? 'Listing' }}</a>
@endsection

@push('styles')

@endpush

@section('content')
<a href="{{ route('dashboard.chat') }}" class="chat-back">← Back to Inbox</a>

<section class="kd-card p0-ovh">
    <div class="chat-wrap">

        {{-- Header --}}
        <div class="chat-header">
            <div class="chat-header-avatar">{{ strtoupper(substr($otherUser->name ?? 'U', 0, 1)) }}</div>
            <div class="chat-header-info">
                <strong>{{ $otherUser->name ?? 'User' }}</strong>
                <span class="chat-header-status" id="chatStatus">Active now</span>
            </div>
        </div>

        {{-- Messages --}}
        <div class="chat-messages" id="chatMessages">
            @forelse($messages as $msg)
                <div class="chat-bubble-wrap {{ $msg->sender_id === Auth::id() ? 'is-mine' : 'is-other' }}"
                     data-msg-id="{{ $msg->id }}">
                    <div class="chat-bubble">{!! nl2br(e($msg->message)) !!}</div>
                    <div class="chat-bubble-meta">
                        <span>{{ $msg->created_at->diffForHumans() }}</span>
                        @if($msg->sender_id === Auth::id())
                            <span class="chat-tick {{ $msg->read_at ? 'seen' : 'delivered' }}"
                                  data-tick="{{ $msg->id }}"
                                  title="{{ $msg->read_at ? 'Seen' : 'Delivered' }}">✓✓</span>
                        @endif
                    </div>
                </div>
            @empty
                <p class="center-empty-kd">
                    No messages yet. Say hello 👋
                </p>
            @endforelse

            {{-- Typing indicator --}}
            <div class="chat-typing" id="chatTyping">
                <span></span><span></span><span></span>
            </div>
        </div>

        {{-- Input bar --}}
        <div id="chatError" class="hidden" class="chat-error"></div>
        <form id="chatForm" class="chat-input-bar" autocomplete="off">
            @csrf
            <textarea
                id="chatInput"
                name="message"
                placeholder="Type a message… (Enter to send, Shift+Enter for new line)"
                maxlength="2000"
                rows="1"
                required></textarea>
            <button type="submit" class="chat-send-btn" id="chatSendBtn">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                Send
            </button>
        </form>

    </div>
</section>
@endsection

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
(function () {
    'use strict';

    const THREAD_ID  = {{ $thread->id }};
    const MY_ID      = {{ Auth::id() }};
    const POLL_MS    = 1000;
    const SEND_URL   = '/api/chat/' + THREAD_ID + '/send';
    const POLL_URL   = '/api/chat/' + THREAD_ID + '/messages?after=';
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content || '';

    let lastId     = {{ $messages->last()?->id ?? 0 }};
    let pollTimer  = null;
    let sending    = false;
    let tabVisible = !document.hidden;
    let soundCtx   = null;

    const msgList  = document.getElementById('chatMessages');
    const form     = document.getElementById('chatForm');
    const input    = document.getElementById('chatInput');
    const sendBtn  = document.getElementById('chatSendBtn');
    const errBox   = document.getElementById('chatError');
    const typing   = document.getElementById('chatTyping');

    // Track rendered real DB ids — single source of truth to prevent duplicates
    const renderedIds = new Set(
        [...msgList.querySelectorAll('[data-msg-id]')]
            .map(el => +el.dataset.msgId)
            .filter(id => id > 0 && id < 1e12)
    );

    // ── Scroll to bottom ──────────────────────────────────────────────────────
    function scrollBottom(force) {
        if (!msgList) return;
        const atBottom = msgList.scrollHeight - msgList.scrollTop - msgList.clientHeight < 120;
        if (force || atBottom) {
            msgList.scrollTop = msgList.scrollHeight;
        }
    }
    scrollBottom(true);

    // ── Notification beep (Web Audio API) ────────────────────────────────────
    function beep() {
        try {
            soundCtx = soundCtx || new (window.AudioContext || window.webkitAudioContext)();
            const o = soundCtx.createOscillator();
            const g = soundCtx.createGain();
            o.connect(g); g.connect(soundCtx.destination);
            o.frequency.setValueAtTime(880, soundCtx.currentTime);
            g.gain.setValueAtTime(0.08, soundCtx.currentTime);
            g.gain.exponentialRampToValueAtTime(0.001, soundCtx.currentTime + 0.25);
            o.start(); o.stop(soundCtx.currentTime + 0.25);
        } catch (_) {}
    }

    // ── Build a message bubble DOM node ──────────────────────────────────────
    function buildBubble(msg) {
        const wrap = document.createElement('div');
        wrap.className  = 'chat-bubble-wrap ' + (msg.is_mine ? 'is-mine' : 'is-other');
        wrap.dataset.msgId = msg.id;

        const text = document.createTextNode(msg.message);
        const bubble = document.createElement('div');
        bubble.className = 'chat-bubble';
        // Render newlines
        msg.message.split('\n').forEach((line, i) => {
            if (i > 0) bubble.appendChild(document.createElement('br'));
            bubble.appendChild(document.createTextNode(line));
        });

        const meta = document.createElement('div');
        meta.className = 'chat-bubble-meta';
        const isSeen = msg.is_mine && msg.read_at;
        meta.innerHTML = '<span>' + msg.time + '</span>'
            + (msg.is_mine
                ? '<span class="chat-tick ' + (isSeen ? 'seen' : 'delivered') + '" data-tick="' + msg.id + '" title="' + (isSeen ? 'Seen' : 'Delivered') + '">✓✓</span>'
                : '');

        wrap.appendChild(bubble);
        wrap.appendChild(meta);
        return wrap;
    }

    // ── Insert bubble before typing indicator ─────────────────────────────────
    // realId: the actual DB id (undefined for optimistic bubbles)
    function insertBubble(msg, realId) {
        if (realId !== undefined) {
            if (renderedIds.has(realId)) return null; // already shown
            renderedIds.add(realId);
            lastId = Math.max(lastId, realId);
        }
        const node = buildBubble(msg);
        msgList.insertBefore(node, typing);
        return node;
    }

    // ── Poll for new messages ─────────────────────────────────────────────────
    function poll() {
        fetch(POLL_URL + lastId, { credentials: 'same-origin' })
            .then(r => r.ok ? r.json() : null)
            .then(data => {
                if (!data) return;
                // Update Seen ticks for my already-rendered messages
                if (data.read_ids && data.read_ids.length) {
                    data.read_ids.forEach(id => {
                        const tick = msgList.querySelector('[data-tick="' + id + '"]');
                        if (tick && !tick.classList.contains('seen')) {
                            tick.classList.remove('delivered');
                            tick.classList.add('seen');
                            tick.title = 'Seen';
                        }
                    });
                }
                if (!data.messages || !data.messages.length) return;
                let hadNew = false;
                data.messages.forEach(m => {
                    const inserted = insertBubble(m, m.id);
                    if (inserted && !m.is_mine) hadNew = true;
                });
                scrollBottom(false);
                if (hadNew && !tabVisible) {
                    beep();
                    document.title = '💬 New message — kegalle';
                }
            })
            .catch(() => {/* network blip — ignore */});
    }

    // ── Start / stop polling based on tab visibility ──────────────────────────
    function startPolling() {
        if (pollTimer) return;
        pollTimer = setInterval(poll, POLL_MS);
    }
    function stopPolling() {
        clearInterval(pollTimer);
        pollTimer = null;
    }

    document.addEventListener('visibilitychange', () => {
        tabVisible = !document.hidden;
        if (tabVisible) {
            document.title = 'Chat — {{ addslashes($otherUser->name ?? "User") }}';
            poll();
            startPolling();
        } else {
            stopPolling();
        }
    });

    poll();         // immediate first check on load
    startPolling();

    // ── Send message ──────────────────────────────────────────────────────────
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const text = input.value.trim();
        if (!text || sending) return;

        sending = true;
        sendBtn.disabled = true;
        errBox.style.display = 'none';

        // Optimistic bubble — no realId so renderedIds/lastId not updated
        const optimistic = insertBubble({
            id:      Date.now(),
            message: text,
            is_mine: true,
            time:    'Just now',
        });
        optimistic.style.opacity = '0.65';
        input.value = '';
        input.style.height = '';
        scrollBottom(true);

        fetch(SEND_URL, {
            method:  'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept':       'application/json',
            },
            body: JSON.stringify({ message: text }),
        })
        .then(r => {
            if (!r.ok) return r.json().then(d => Promise.reject(d.message || 'Send failed'));
            return r.json();
        })
        .then(msg => {
            if (renderedIds.has(msg.id)) {
                // Poll already inserted the real bubble — just remove optimistic
                optimistic.remove();
            } else {
                // Register it and replace optimistic with real bubble
                renderedIds.add(msg.id);
                lastId = Math.max(lastId, msg.id);
                const real = buildBubble(msg);
                if (msgList.contains(optimistic)) {
                    msgList.replaceChild(real, optimistic);
                } else {
                    optimistic.remove();
                    msgList.insertBefore(real, typing);
                }
            }
        })
        .catch(err => {
            optimistic.remove();
            input.value = text; // restore
            showError(typeof err === 'string' ? err : 'Failed to send. Please try again.');
        })
        .finally(() => {
            sending = false;
            sendBtn.disabled = false;
            input.focus();
        });
    });

    // ── Auto-grow textarea ────────────────────────────────────────────────────
    input.addEventListener('input', function () {
        this.style.height = '';
        this.style.height = Math.min(this.scrollHeight, 120) + 'px';
    });

    // ── Enter sends, Shift+Enter inserts newline ──────────────────────────────
    input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            form.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
        }
    });

    // ── Error display ─────────────────────────────────────────────────────────
    function showError(msg) {
        errBox.textContent = msg;
        errBox.style.display = 'block';
        setTimeout(() => { errBox.style.display = 'none'; }, 5000);
    }

})();
</script>
@endpush
