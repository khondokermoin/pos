@php
    $flashMessages = [];

    if (session('success')) {
        $flashMessages[] = ['type' => 'success', 'message' => session('success')];
    }

    if (session('info')) {
        $flashMessages[] = ['type' => 'info', 'message' => session('info')];
    }

    if (session('warning')) {
        $flashMessages[] = ['type' => 'warning', 'message' => session('warning')];
    }

    if (session('error')) {
        $flashMessages[] = ['type' => 'error', 'message' => session('error')];
    }

    if ($errors->any()) {
        foreach ($errors->all() as $error) {
            $flashMessages[] = ['type' => 'error', 'message' => $error, 'title' => 'Validation Error'];
        }
    }
@endphp

@if (!empty($flashMessages))
    <div id="flash-toast-container"
        style="position:fixed;top:20px;right:20px;z-index:99999;display:flex;flex-direction:column;gap:10px;max-width:360px;pointer-events:none;">
        @foreach ($flashMessages as $flash)
            @php
                $type = $flash['type'] ?? 'info';
                $message = $flash['message'] ?? '';
                $title = $flash['title'] ?? null;

                $styles = [
                    'success' => [
                        'bg' => '#f0fdf4',
                        'border' => '#86efac',
                        'icon' => '#16a34a',
                        'title' => '#15803d',
                        'text' => '#166534',
                        'label' => 'Success',
                        'progress' => '#16a34a',
                        'svg' =>
                            '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12.75 11.25 15 15 9.75" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="9" stroke-linecap="round" stroke-linejoin="round"/></svg>',
                    ],
                    'error' => [
                        'bg' => '#fff1f2',
                        'border' => '#fca5a5',
                        'icon' => '#dc2626',
                        'title' => '#b91c1c',
                        'text' => '#991b1b',
                        'label' => 'Error',
                        'progress' => '#dc2626',
                        'svg' =>
                            '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9" stroke-linecap="round" stroke-linejoin="round"/><path d="M9.75 9.75l4.5 4.5M14.25 9.75l-4.5 4.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
                    ],
                    'warning' => [
                        'bg' => '#fffbeb',
                        'border' => '#fcd34d',
                        'icon' => '#d97706',
                        'title' => '#b45309',
                        'text' => '#92400e',
                        'label' => 'Warning',
                        'progress' => '#d97706',
                        'svg' =>
                            '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 8.75v4.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M12 15.9h.01" stroke-linecap="round" stroke-linejoin="round"/><path d="M10.24 3.76 2.94 16.75A2.25 2.25 0 0 0 5 20.25h14a2.25 2.25 0 0 0 1.95-3.5L13.76 3.76a2.25 2.25 0 0 0-3.52 0Z" stroke-linecap="round" stroke-linejoin="round"/></svg>',
                    ],
                    'info' => [
                        'bg' => '#f0f9ff',
                        'border' => '#7dd3fc',
                        'icon' => '#0284c7',
                        'title' => '#0369a1',
                        'text' => '#075985',
                        'label' => 'Information',
                        'progress' => '#0284c7',
                        'svg' =>
                            '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9" stroke-linecap="round" stroke-linejoin="round"/><path d="M12 11.5v4.25" stroke-linecap="round" stroke-linejoin="round"/><path d="M12 8.25h.01" stroke-linecap="round" stroke-linejoin="round"/></svg>',
                    ],
                ];

                $s = $styles[$type] ?? $styles['info'];
                $finalTitle = $title ?: $s['label'];
            @endphp

            <div class="flash-toast-item"
                style="pointer-events:auto;background:{{ $s['bg'] }};border:1px solid {{ $s['border'] }};border-radius:12px;box-shadow:0 4px 16px rgba(0,0,0,0.10);padding:14px 16px 10px 16px;display:flex;flex-direction:column;gap:0;opacity:1;transition:opacity 0.3s ease,transform 0.3s ease;transform:translateX(0);min-width:280px;max-width:360px;overflow:hidden;">

                {{-- Top row: icon + text + close --}}
                <div style="display:flex;align-items:flex-start;gap:12px;">

                    {{-- Icon --}}
                    <div
                        style="flex-shrink:0;width:36px;height:36px;border-radius:8px;background:{{ $s['icon'] }}22;display:flex;align-items:center;justify-content:center;color:{{ $s['icon'] }};">
                        {!! $s['svg'] !!}
                    </div>

                    {{-- Text --}}
                    <div style="flex:1;min-width:0;">
                        <p
                            style="margin:0 0 2px 0;font-size:13px;font-weight:600;color:{{ $s['title'] }};line-height:1.4;">
                            {{ $finalTitle }}</p>
                        <p
                            style="margin:0;font-size:13px;color:{{ $s['text'] }};line-height:1.5;word-break:break-word;">
                            {{ $message }}</p>
                    </div>

                    {{-- Close button --}}
                    <button
                        onclick="(function(el){el.style.opacity='0';el.style.transform='translateX(20px)';setTimeout(function(){if(el.parentNode)el.remove();},300);})(this.closest('.flash-toast-item'))"
                        style="flex-shrink:0;background:none;border:none;cursor:pointer;padding:2px;color:{{ $s['text'] }};opacity:0.6;line-height:1;font-size:18px;margin-top:-2px;"
                        aria-label="Close">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5">
                            <path d="M6 6l12 12M18 6L6 18" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                </div>

                {{-- Progress bar --}}
                <div
                    style="margin-top:10px;height:3px;border-radius:2px;background:{{ $s['border'] }};overflow:hidden;">
                    <div class="toast-progress-bar"
                        style="height:100%;width:100%;background:{{ $s['progress'] }};border-radius:2px;transform-origin:left;transition:none;">
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <script>
        (function() {
            var DURATION = 5000; // মিলিসেকেন্ড (5 সেকেন্ড)

            document.addEventListener('DOMContentLoaded', function() {
                var items = document.querySelectorAll('#flash-toast-container .flash-toast-item');
                items.forEach(function(item, index) {
                    var delay = index * 400;
                    var bar = item.querySelector('.toast-progress-bar');

                    // Progress bar অ্যানিমেশন শুরু করো
                    setTimeout(function() {
                        if (bar) {
                            bar.style.transition = 'transform ' + DURATION + 'ms linear';
                            bar.style.transform = 'scaleX(0)';
                        }
                    }, delay + 50); // ছোট্ট delay দিয়ে transition শুরু

                    // Toast সরিয়ে দাও
                    setTimeout(function() {
                        item.style.opacity = '0';
                        item.style.transform = 'translateX(20px)';
                        setTimeout(function() {
                            if (item.parentNode) item.remove();
                        }, 300);
                    }, delay + DURATION);
                });
            });
        })();
    </script>
@endif
