<!-- Vendor JS -->
<script src="{{ asset('frontend_assets/js/vendor.min.js') }}"></script>

<!-- Application JS -->
<script src="{{ asset('frontend_assets/js/app.js') }}"></script>

<!-- ApexCharts -->
<script src="{{ asset('frontend_assets/vendor/apexcharts/apexcharts.min.js') }}"></script>

<!-- Dashboard JS -->
<script src="{{ asset('frontend_assets/js/pages/dashboard.js') }}"></script>

<!-- jQuery (Load only if not already included in vendor.min.js) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script>
    (function() {
        /* ─── colour palette (inline-style based, no Tailwind/Bootstrap needed) ─── */
        var palette = {
            success: {
                bg: '#f0fdf4',
                border: '#86efac',
                icon: '#16a34a',
                title: '#15803d',
                text: '#166534',
                label: 'Success',
                svg: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12.75 11.25 15 15 9.75" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="9" stroke-linecap="round" stroke-linejoin="round"/></svg>'
            },
            error: {
                bg: '#fff1f2',
                border: '#fca5a5',
                icon: '#dc2626',
                title: '#b91c1c',
                text: '#991b1b',
                label: 'Error',
                svg: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9" stroke-linecap="round" stroke-linejoin="round"/><path d="M9.75 9.75l4.5 4.5M14.25 9.75l-4.5 4.5" stroke-linecap="round" stroke-linejoin="round"/></svg>'
            },
            warning: {
                bg: '#fffbeb',
                border: '#fcd34d',
                icon: '#d97706',
                title: '#b45309',
                text: '#92400e',
                label: 'Warning',
                svg: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 8.75v4.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M12 15.9h.01" stroke-linecap="round" stroke-linejoin="round"/><path d="M10.24 3.76 2.94 16.75A2.25 2.25 0 0 0 5 20.25h14a2.25 2.25 0 0 0 1.95-3.5L13.76 3.76a2.25 2.25 0 0 0-3.52 0Z" stroke-linecap="round" stroke-linejoin="round"/></svg>'
            },
            info: {
                bg: '#f0f9ff',
                border: '#7dd3fc',
                icon: '#0284c7',
                title: '#0369a1',
                text: '#075985',
                label: 'Information',
                svg: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9" stroke-linecap="round" stroke-linejoin="round"/><path d="M12 11.5v4.25" stroke-linecap="round" stroke-linejoin="round"/><path d="M12 8.25h.01" stroke-linecap="round" stroke-linejoin="round"/></svg>'
            }
        };

        function escapeHtml(value) {
            return String(value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        /* ─── get or create the fixed toast container ─── */
        function getContainer() {
            var existing = document.getElementById('flash-toast-container');
            if (existing) return existing;

            var container = document.createElement('div');
            container.id = 'flash-toast-container';
            container.setAttribute('aria-live', 'polite');
            container.setAttribute('aria-atomic', 'true');
            container.style.cssText =
                'position:fixed;bottom:20px;right:20px;z-index:99999;display:flex;flex-direction:column;gap:10px;max-width:360px;pointer-events:none;';
            document.body.appendChild(container);
            return container;
        }

        /* ─── build and show a single toast ─── */
        function appendSmartToast(detail) {
            detail = detail || {};
            var type = detail.type || 'info';
            var message = detail.message || '';
            var title = detail.title || null;

            if (!message || !String(message).trim()) return;

            var p = palette[type] || palette.info;
            var finalTitle = title || p.label;

            /* wrapper */
            var item = document.createElement('div');
            item.className = 'flash-toast-item';
            item.style.cssText = [
                'pointer-events:auto',
                'background:' + p.bg,
                'border:1px solid ' + p.border,
                'border-radius:12px',
                'box-shadow:0 4px 16px rgba(0,0,0,0.10)',
                'padding:14px 16px',
                'display:flex',
                'align-items:flex-start',
                'gap:12px',
                'opacity:0',
                'transform:translateX(20px)',
                'transition:opacity 0.3s ease,transform 0.3s ease',
                'min-width:280px',
                'max-width:360px'
            ].join(';');

            item.innerHTML =
                /* icon */
                '<div style="flex-shrink:0;width:36px;height:36px;border-radius:8px;background:' + p.icon +
                '22;display:flex;align-items:center;justify-content:center;color:' + p.icon + ';">' +
                p.svg +
                '</div>' +
                /* text */
                '<div style="flex:1;min-width:0;">' +
                '<p style="margin:0 0 2px 0;font-size:13px;font-weight:600;color:' + p.title +
                ';line-height:1.4;">' + escapeHtml(finalTitle) + '</p>' +
                '<p style="margin:0;font-size:13px;color:' + p.text + ';line-height:1.5;word-break:break-word;">' +
                escapeHtml(message) + '</p>' +
                '</div>' +
                /* close button */
                '<button onclick="(function(el){el.style.opacity=\'0\';el.style.transform=\'translateX(20px)\';setTimeout(function(){if(el.parentNode)el.remove();},300);})(this.closest(\'.flash-toast-item\'))" ' +
                'style="flex-shrink:0;background:none;border:none;cursor:pointer;padding:2px;color:' + p.text +
                ';opacity:0.6;line-height:1;margin-top:-2px;" ' +
                'aria-label="Close">' +
                '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 6l12 12M18 6L6 18" stroke-linecap="round" stroke-linejoin="round"/></svg>' +
                '</button>';

            var container = getContainer();
            container.appendChild(item);

            /* animate in */
            requestAnimationFrame(function() {
                requestAnimationFrame(function() {
                    item.style.opacity = '1';
                    item.style.transform = 'translateX(0)';
                });
            });

            /* auto-dismiss after 4 s */
            setTimeout(function() {
                item.style.opacity = '0';
                item.style.transform = 'translateX(20px)';
                setTimeout(function() {
                    if (item.parentNode) item.remove();
                }, 300);
            }, 4000);
        }

        /* ─── public API ─── */
        window.smartNotify = function(type, message, title) {
            window.dispatchEvent(new CustomEvent('notify', {
                detail: {
                    type: type || 'info',
                    message: message || '',
                    title: title || null
                }
            }));
        };

        window.notify = window.smartNotify;
        window.showSmartToast = window.smartNotify;

        window.addEventListener('notify', function(event) {
            appendSmartToast(event.detail || {});
        });
    })();
</script>
