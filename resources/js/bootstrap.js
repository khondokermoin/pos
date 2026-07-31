import axios from 'axios';
import Alpine from 'alpinejs';

window.axios = axios;
window.Alpine = Alpine;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

Alpine.start();

(function () {
    const palette = {
        success: {
            panel: 'border-emerald-200 bg-emerald-50',
            iconWrap: 'bg-emerald-100',
            icon: 'text-emerald-600',
            text: 'text-emerald-800',
            textSoft: 'text-emerald-700/80',
            title: 'Success',
            svg: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5" aria-hidden="true"><path d="M9 12.75 11.25 15 15 9.75" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="8.25" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        },
        error: {
            panel: 'border-rose-200 bg-rose-50',
            iconWrap: 'bg-rose-100',
            icon: 'text-rose-600',
            text: 'text-rose-800',
            textSoft: 'text-rose-700/80',
            title: 'Error',
            svg: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5" aria-hidden="true"><circle cx="12" cy="12" r="8.25" stroke-linecap="round" stroke-linejoin="round"/><path d="M9.75 9.75l4.5 4.5M14.25 9.75l-4.5 4.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        },
        warning: {
            panel: 'border-amber-200 bg-amber-50',
            iconWrap: 'bg-amber-100',
            icon: 'text-amber-600',
            text: 'text-amber-800',
            textSoft: 'text-amber-700/80',
            title: 'Warning',
            svg: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5" aria-hidden="true"><path d="M12 8.75v4.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M12 15.9h.01" stroke-linecap="round" stroke-linejoin="round"/><path d="M10.24 3.76 2.94 16.75A2.25 2.25 0 0 0 5 20.25h14a2.25 2.25 0 0 0 1.95-3.5L13.76 3.76a2.25 2.25 0 0 0-3.52 0Z" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        },
        info: {
            panel: 'border-sky-200 bg-sky-50',
            iconWrap: 'bg-sky-100',
            icon: 'text-sky-600',
            text: 'text-sky-800',
            textSoft: 'text-sky-700/80',
            title: 'Information',
            svg: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5" aria-hidden="true"><circle cx="12" cy="12" r="8.25" stroke-linecap="round" stroke-linejoin="round"/><path d="M12 11.5v4.25" stroke-linecap="round" stroke-linejoin="round"/><path d="M12 8.25h.01" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        },
    };

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function getContainer() {
        const existing = document.querySelector('[data-smart-toast-container]');
        if (existing) return existing;

        const container = document.createElement('div');
        container.setAttribute('aria-live', 'polite');
        container.setAttribute('aria-atomic', 'true');
        container.className = 'pointer-events-none fixed bottom-5 right-5 z-50 flex max-w-sm flex-col gap-3';
        container.setAttribute('data-smart-toast-container', 'true');
        document.body.appendChild(container);
        return container;
    }

    function appendToast(detail = {}) {
        const type = detail.type || 'info';
        const message = detail.message || '';
        const title = detail.title || palette[type]?.title || 'Notification';

        if (!message || !String(message).trim()) return;

        const option = palette[type] || palette.info;
        const wrapper = document.createElement('div');
        wrapper.className = 'pointer-events-auto w-full max-w-sm';
        wrapper.setAttribute('x-data', '{ show: true }');
        wrapper.setAttribute('x-init', 'setTimeout(() => show = false, 4000)');
        wrapper.setAttribute('x-show', 'show');
        wrapper.setAttribute('x-transition:enter', 'transition ease-out duration-300');
        wrapper.setAttribute('x-transition:enter-start', 'opacity-0 translate-x-6');
        wrapper.setAttribute('x-transition:enter-end', 'opacity-100 translate-x-0');
        wrapper.setAttribute('x-transition:leave', 'transition ease-in duration-200');
        wrapper.setAttribute('x-transition:leave-start', 'opacity-100 translate-x-0');
        wrapper.setAttribute('x-transition:leave-end', 'opacity-0 translate-x-6');
        wrapper.innerHTML = `
            <div class="rounded-xl border shadow-lg shadow-gray-200/50 ${option.panel} ring-1 ring-white/60 backdrop-blur-sm">
                <div class="flex items-start gap-3 p-3.5">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg ${option.iconWrap} ${option.icon}">${option.svg}</div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold ${option.text}">${escapeHtml(title)}</p>
                        <p class="mt-1 text-sm leading-5 ${option.textSoft} break-words">${escapeHtml(message)}</p>
                    </div>
                    <button type="button" class="ml-2 inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full border border-transparent text-sm font-medium ${option.textSoft} transition hover:bg-black/5 focus:outline-none focus:ring-2 focus:ring-slate-300/60" aria-label="Close notification" @click="show = false">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </div>
            </div>
        `;

        const container = getContainer();
        container.appendChild(wrapper);

        if (window.Alpine) {
            window.Alpine.initTree(wrapper);
        }
    }

    window.smartNotify = function (type = 'info', message = '', title = null) {
        window.dispatchEvent(new CustomEvent('notify', {
            detail: { type, message, title },
        }));
    };

    window.showSmartToast = window.smartNotify;
    window.notify = window.smartNotify;

    window.addEventListener('notify', function (event) {
        appendToast(event.detail || {});
    });
})();
