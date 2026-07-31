{{-- ============================================================
     CLOUD POS — FULL-SCREEN TERMINAL  v4
     CSS  : public/frontend_assets/css/pos-terminal.css
     Stack: Bootstrap 5 (Tabler) + Vanilla JS
     Fixes: server-authoritative pricing, split payments, hold/recall,
            quick-add customer, per-line rounding, change display,
            keyboard help overlay, ARIA, error recovery UX.
     ============================================================ --}}
<!DOCTYPE html>
<html lang="en" data-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>POS Terminal — {{ auth()->user()->branch->name ?? 'Branch' }}</title>
    <link rel="stylesheet" href="{{ asset('frontend_assets/css/app.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend_assets/css/icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend_assets/css/pos-terminal.css') }}">
</head>

<body>

    {{-- ═══════════════════════════════════════════════════════════
     POS APP SHELL
     ═══════════════════════════════════════════════════════════ --}}
    <div id="pos-app">

        {{-- ── TOPBAR ── --}}
        <header id="pos-topbar" role="banner">
            <span class="pos-brand">
                <i class="ti ti-device-desktop-analytics" aria-hidden="true"></i> CloudPOS
            </span>
            <span class="branch-badge">
                <i class="ti ti-building-store" style="font-size:12px;" aria-hidden="true"></i>
                {{ auth()->user()->branch->name ?? 'Branch Terminal' }}
            </span>
            <div class="tb-divider"></div>

            <div id="barcode-wrap" role="search">
                <i class="ti ti-barcode scan-icon" aria-hidden="true"></i>
                <input type="text" id="barcodeInput" placeholder="Scan barcode or search product… (F2)"
                    autocomplete="off" autocorrect="off" spellcheck="false"
                    aria-label="Barcode scanner or product search">
            </div>

            <div class="tb-spacer"></div>
            <span id="pos-clock" aria-live="off" aria-label="Current time"></span>
            <div class="tb-divider"></div>

            <button id="theme-toggle" class="tb-btn" title="Toggle Light / Dark mode" aria-label="Toggle theme">
                <i class="ti ti-sun" id="theme-icon" aria-hidden="true"></i>
                <span id="theme-label">Light</span>
            </button>
            <div class="tb-divider"></div>

            {{-- Hold Queue button --}}
            <button id="btn-show-held" class="tb-btn" title="Held Orders (F6)" aria-label="Show held orders">
                <i class="ti ti-stack-2" aria-hidden="true"></i>
                <span>Held</span>
                <span id="held-badge" class="held-count" style="display:none;">0</span>
            </button>
            <div class="tb-divider"></div>

            {{-- Keyboard help --}}
            <button id="btn-help" class="tb-btn" title="Keyboard shortcuts (?)" aria-label="Show keyboard shortcuts">
                <i class="ti ti-keyboard" aria-hidden="true"></i>
            </button>
            <div class="tb-divider"></div>

            <a href="{{ route('branch.dashboard') }}" class="tb-btn">
                <i class="ti ti-layout-dashboard" aria-hidden="true"></i> <span>Dashboard</span>
            </a>
            <a href="{{ route('branch.sales.index') }}" class="tb-btn">
                <i class="ti ti-receipt" aria-hidden="true"></i> <span>Sales</span>
            </a>
            <a href="{{ route('branch.shifts.index') }}" class="tb-btn">
                <i class="ti ti-clock" aria-hidden="true"></i> <span>Shift</span>
            </a>
        </header>

        {{-- ── CATEGORY BAR (col 1, row 2) ── --}}
        <div id="category-bar" role="navigation" aria-label="Product categories">
            <button class="cat-btn active" data-cat="">
                <i class="ti ti-apps" style="font-size:12px;" aria-hidden="true"></i> All
            </button>
            @foreach ($categories as $cat)
                <button class="cat-btn" data-cat="{{ $cat->id }}">{{ $cat->name }}</button>
            @endforeach
        </div>

        {{-- ── PRODUCT PANEL (col 1, row 3) ── --}}
        <main id="product-panel" role="main" aria-label="Product grid">
            <div id="product-grid-wrap">
                <div id="product-grid" role="list">
                    <div id="grid-loading" role="status" aria-live="polite">
                        <div class="spin" aria-hidden="true"></div>
                        <div style="font-size:13px;">Loading products…</div>
                    </div>
                </div>
            </div>
        </main>

        {{-- ── CART PANEL (col 2, rows 2-3) ── --}}
        <aside id="cart-panel" aria-label="Shopping cart">

            <div id="cart-header">
                <div class="cart-title">
                    <i class="ti ti-shopping-cart" style="font-size:18px;color:var(--accent);"
                        aria-hidden="true"></i>
                    Current Order
                    <span id="cart-count" aria-label="Items in cart">0</span>
                </div>
                <div style="display:flex;gap:6px;">
                    <button id="btn-hold-order" class="tb-btn" title="Hold order (F6)"
                        aria-label="Hold current order" style="padding:4px 9px;font-size:11px;" disabled>
                        <i class="ti ti-player-pause" aria-hidden="true"></i> Hold
                    </button>
                    <button id="btn-clear-cart" title="Clear cart (F8)" aria-label="Clear cart">
                        <i class="ti ti-trash" aria-hidden="true"></i> Clear
                    </button>
                </div>
            </div>

            {{-- Customer row with quick-add --}}
            <div id="customer-row">
                <div style="display:flex;gap:6px;align-items:center;">
                    <select id="customerSelect" aria-label="Select customer" style="flex:1;">
                        <option value="">👤 Walk-in Customer</option>
                        @foreach ($customers as $cust)
                            <option value="{{ $cust->id }}">
                                {{ $cust->name }}{{ $cust->phone ? ' · ' . $cust->phone : '' }}
                            </option>
                        @endforeach
                    </select>
                    <button id="btn-add-customer" class="tb-btn" title="Add new customer"
                        aria-label="Add new customer" style="padding:5px 8px;flex-shrink:0;">
                        <i class="ti ti-user-plus" aria-hidden="true"></i>
                    </button>
                </div>
            </div>

            <div id="cart-items-wrap">
                <div id="cart-empty" role="status">
                    <i class="ti ti-shopping-cart-off" aria-hidden="true"></i>
                    <p>Your cart is empty.<br>Click a product or scan a barcode to add items.</p>
                </div>
                <div id="cart-items-list" style="display:none;" role="list" aria-label="Cart items"></div>
            </div>

            <div id="cart-footer">
                <div class="total-row">
                    <span>Subtotal</span>
                    <span class="val">৳<span id="disp-subtotal">0.00</span></span>
                </div>
                <div class="total-row" id="tax-row" style="display:none;">
                    <span>Tax / VAT</span>
                    <span class="val" style="color:var(--warning);">৳<span id="disp-tax">0.00</span></span>
                </div>
                <div id="discount-row">
                    <label for="discountInput">
                        <i class="ti ti-tag" style="font-size:13px;" aria-hidden="true"></i> Discount (৳)
                    </label>
                    <input type="number" id="discountInput" value="0" min="0" step="0.01"
                        placeholder="0.00" aria-label="Discount amount">
                </div>
                <div class="total-row grand">
                    <span>Total Payable</span>
                    <span class="val">৳<span id="disp-total">0.00</span></span>
                </div>
                <button id="btn-checkout" disabled aria-label="Proceed to checkout (F4)">
                    <i class="ti ti-credit-card" style="font-size:18px;" aria-hidden="true"></i>
                    Proceed to Checkout
                    <small style="opacity:.7;font-weight:400;font-size:11px;">(F4)</small>
                </button>
            </div>

        </aside>

    </div>{{-- /#pos-app --}}


    {{-- ═══════════════════════════════════════════════════════════
     CHECKOUT MODAL
     ═══════════════════════════════════════════════════════════ --}}
    <div id="modal-overlay" role="dialog" aria-modal="true" aria-labelledby="modal-title">
        <div id="checkout-modal">
            <div class="modal-head">
                <h5 id="modal-title"><i class="ti ti-credit-card" style="color:var(--accent);"
                        aria-hidden="true"></i> Complete Payment</h5>
                <button class="modal-close" id="btn-close-modal" aria-label="Close checkout modal">✕</button>
            </div>
            <div class="modal-body">

                <div class="modal-summary" id="modal-summary-box">
                    <div class="sum-row">
                        <span>Subtotal</span>
                        <span class="val">৳<span id="m-subtotal">0.00</span></span>
                    </div>
                    <div class="sum-row" id="m-tax-row" style="display:none;">
                        <span>Tax / VAT</span>
                        <span class="val" style="color:var(--warning);">৳<span id="m-tax">0.00</span></span>
                    </div>
                    <div class="sum-row" id="m-disc-row" style="display:none;">
                        <span>Discount</span>
                        <span class="val" style="color:var(--success);">-৳<span id="m-discount">0.00</span></span>
                    </div>
                    <div class="sum-row total">
                        <span>Total</span>
                        <span class="val">৳<span id="m-total">0.00</span></span>
                    </div>
                </div>

                {{-- Server total mismatch warning (shown if server recalculates differently) --}}
                <div id="price-mismatch-warn" style="display:none;" role="alert">
                    <i class="ti ti-alert-triangle" aria-hidden="true"></i>
                    <span id="price-mismatch-msg"></span>
                </div>

                <label class="modal-label">Payment Method</label>
                <div class="pay-methods" role="group" aria-label="Payment method">
                    <div class="pay-btn selected" data-method="cash" role="button" tabindex="0"
                        aria-pressed="true"><i class="ti ti-cash" aria-hidden="true"></i> Cash</div>
                    <div class="pay-btn" data-method="card" role="button" tabindex="0" aria-pressed="false"><i
                            class="ti ti-credit-card" aria-hidden="true"></i> Card</div>
                    <div class="pay-btn" data-method="mobile_banking" role="button" tabindex="0"
                        aria-pressed="false"><i class="ti ti-device-mobile" aria-hidden="true"></i> Mobile</div>
                </div>

                <label class="modal-label" for="receivedAmount">Received Amount (৳)</label>
                <input type="number" id="receivedAmount" placeholder="0.00" min="0" step="0.01"
                    aria-label="Amount received from customer">

                <div class="quick-cash" id="quick-cash-btns" aria-label="Quick cash amounts"></div>

                <div id="change-display" aria-live="polite">
                    <span class="lbl" id="change-label"><i class="ti ti-coins" aria-hidden="true"></i> Change to
                        Return</span>
                    <span id="change-amount" aria-label="Change amount">৳0.00</span>
                </div>

                <button id="btn-confirm-sale" disabled aria-label="Confirm and complete sale">
                    <div class="spin-sm" aria-hidden="true"></div>
                    <span class="btn-text"><i class="ti ti-check" aria-hidden="true"></i> Confirm &amp; Complete
                        Sale</span>
                </button>

            </div>
        </div>
    </div>


    {{-- ═══════════════════════════════════════════════════════════
     SUCCESS OVERLAY
     ═══════════════════════════════════════════════════════════ --}}
    <div id="success-overlay" role="dialog" aria-modal="true" aria-labelledby="success-title">
        <div class="success-icon" aria-hidden="true"><i class="ti ti-check"></i></div>
        <div class="success-title" id="success-title">Sale Complete!</div>
        <div class="success-invoice" id="success-invoice-no" aria-live="polite"></div>
        <div style="font-size:14px;color:var(--text-2);">Change to return:</div>
        <div class="success-change" id="success-change" aria-live="polite">৳0.00</div>
        <div class="success-actions">
            <button class="suc-btn print" id="btn-print-invoice" aria-label="Print invoice">
                <i class="ti ti-printer" aria-hidden="true"></i> Print Invoice
            </button>
            <button class="suc-btn new" id="btn-new-sale" aria-label="Start new sale">
                <i class="ti ti-plus" aria-hidden="true"></i> New Sale
            </button>
        </div>
    </div>


    {{-- ═══════════════════════════════════════════════════════════
     HELD ORDERS PANEL (slide-in)
     ═══════════════════════════════════════════════════════════ --}}
    <div id="held-panel-overlay">
        <div id="held-panel" role="dialog" aria-modal="true" aria-labelledby="held-panel-title">
            <div class="modal-head">
                <h5 id="held-panel-title"><i class="ti ti-stack-2" style="color:var(--accent);"
                        aria-hidden="true"></i> Held Orders</h5>
                <button class="modal-close" id="btn-close-held" aria-label="Close held orders panel">✕</button>
            </div>
            <div id="held-list" style="padding:12px 16px;overflow-y:auto;flex:1;">
                <div id="held-empty" style="text-align:center;padding:40px 20px;color:var(--text-3);">
                    <i class="ti ti-stack-2" style="font-size:36px;opacity:.3;display:block;margin-bottom:8px;"
                        aria-hidden="true"></i>
                    No held orders.
                </div>
            </div>
        </div>
    </div>


    {{-- ═══════════════════════════════════════════════════════════
     HOLD ORDER LABEL MODAL
     ═══════════════════════════════════════════════════════════ --}}
    <div id="hold-label-overlay">
        <div id="hold-label-modal" role="dialog" aria-modal="true" aria-labelledby="hold-label-title">
            <div class="modal-head">
                <h5 id="hold-label-title"><i class="ti ti-player-pause" style="color:var(--warning);"
                        aria-hidden="true"></i> Hold Order</h5>
                <button class="modal-close" id="btn-close-hold-label" aria-label="Cancel hold">✕</button>
            </div>
            <div class="modal-body">
                <label class="modal-label" for="holdLabelInput">Label / Note (optional)</label>
                <input type="text" id="holdLabelInput" placeholder="e.g. Table 3, Customer name…" maxlength="100"
                    style="width:100%;background:var(--card-bg);border:1.5px solid var(--border);border-radius:8px;color:var(--text-1);padding:9px 12px;font-size:14px;outline:none;"
                    aria-label="Hold order label">
                <button id="btn-confirm-hold"
                    style="width:100%;margin-top:14px;padding:12px;border-radius:10px;border:none;background:linear-gradient(135deg,var(--warning),#c87800);color:#fff;font-size:14px;font-weight:700;cursor:pointer;">
                    <i class="ti ti-player-pause" aria-hidden="true"></i> Hold This Order
                </button>
            </div>
        </div>
    </div>


    {{-- ═══════════════════════════════════════════════════════════
     QUICK-ADD CUSTOMER MODAL
     ═══════════════════════════════════════════════════════════ --}}
    <div id="add-customer-overlay">
        <div id="add-customer-modal" role="dialog" aria-modal="true" aria-labelledby="add-customer-title">
            <div class="modal-head">
                <h5 id="add-customer-title"><i class="ti ti-user-plus" style="color:var(--accent);"
                        aria-hidden="true"></i> Add New Customer</h5>
                <button class="modal-close" id="btn-close-add-customer"
                    aria-label="Close add customer modal">✕</button>
            </div>
            <div class="modal-body">
                <label class="modal-label" for="newCustName">Name <span style="color:var(--danger);">*</span></label>
                <input type="text" id="newCustName" placeholder="Customer name" maxlength="100"
                    style="width:100%;background:var(--card-bg);border:1.5px solid var(--border);border-radius:8px;color:var(--text-1);padding:9px 12px;font-size:14px;outline:none;margin-bottom:10px;"
                    aria-label="Customer name" aria-required="true">

                <label class="modal-label" for="newCustPhone">Phone</label>
                <input type="tel" id="newCustPhone" placeholder="Phone number" maxlength="30"
                    style="width:100%;background:var(--card-bg);border:1.5px solid var(--border);border-radius:8px;color:var(--text-1);padding:9px 12px;font-size:14px;outline:none;margin-bottom:10px;"
                    aria-label="Customer phone">

                <label class="modal-label" for="newCustEmail">Email</label>
                <input type="email" id="newCustEmail" placeholder="Email address" maxlength="100"
                    style="width:100%;background:var(--card-bg);border:1.5px solid var(--border);border-radius:8px;color:var(--text-1);padding:9px 12px;font-size:14px;outline:none;margin-bottom:14px;"
                    aria-label="Customer email">

                <div id="add-cust-error" role="alert"
                    style="display:none;color:var(--danger);font-size:12px;margin-bottom:10px;"></div>

                <button id="btn-save-customer"
                    style="width:100%;padding:12px;border-radius:10px;border:none;background:linear-gradient(135deg,var(--accent),#6c3de0);color:#fff;font-size:14px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;">
                    <div class="spin-sm" id="cust-spin" aria-hidden="true"></div>
                    <span id="cust-btn-text"><i class="ti ti-check" aria-hidden="true"></i> Save Customer</span>
                </button>
            </div>
        </div>
    </div>


    {{-- ═══════════════════════════════════════════════════════════
     KEYBOARD SHORTCUTS HELP OVERLAY
     ═══════════════════════════════════════════════════════════ --}}
    <div id="help-overlay" role="dialog" aria-modal="true" aria-labelledby="help-title">
        <div id="help-modal">
            <div class="modal-head">
                <h5 id="help-title"><i class="ti ti-keyboard" style="color:var(--accent);" aria-hidden="true"></i>
                    Keyboard Shortcuts</h5>
                <button class="modal-close" id="btn-close-help" aria-label="Close help">✕</button>
            </div>
            <div class="modal-body">
                <table class="shortcut-table" aria-label="Keyboard shortcuts">
                    <tbody>
                        <tr>
                            <td><kbd>F2</kbd></td>
                            <td>Focus barcode / search input</td>
                        </tr>
                        <tr>
                            <td><kbd>F4</kbd></td>
                            <td>Open checkout (when cart has items)</td>
                        </tr>
                        <tr>
                            <td><kbd>F6</kbd></td>
                            <td>Hold current order / Show held orders</td>
                        </tr>
                        <tr>
                            <td><kbd>F8</kbd></td>
                            <td>Clear cart</td>
                        </tr>
                        <tr>
                            <td><kbd>Enter</kbd></td>
                            <td>Search / add product (in barcode field)</td>
                        </tr>
                        <tr>
                            <td><kbd>Esc</kbd></td>
                            <td>Close any open modal</td>
                        </tr>
                        <tr>
                            <td><kbd>?</kbd></td>
                            <td>Show this help</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    {{-- ═══════════════════════════════════════════════════════════
     TOAST CONTAINER
     ═══════════════════════════════════════════════════════════ --}}
    <div data-smart-toast-container aria-live="polite" aria-atomic="true" class="pointer-events-none fixed bottom-5 right-5 z-50 flex max-w-sm flex-col gap-3"></div>


    {{-- ═══════════════════════════════════════════════════════════
     JAVASCRIPT ENGINE
     ═══════════════════════════════════════════════════════════ --}}
    <script>
        'use strict';

        // ── CONFIG ──────────────────────────────────────────────────
        const ROUTES = {
            search: '{{ route('branch.pos.search') }}',
            products: '{{ route('branch.pos.products') }}',
            checkout: '{{ route('branch.pos.checkout') }}',
            quickCust: '{{ route('branch.pos.customers.quick-create') }}',
            hold: '{{ route('branch.pos.hold') }}',
            heldOrders: '{{ route('branch.pos.held-orders') }}',
            deleteHeld: '{{ url('branch/pos/held-orders') }}', // + /{id}
        };
        const CSRF = document.querySelector('meta[name="csrf-token"]').content;
        const INITIAL_PRODUCTS = @json($products);

        // ── STATE ───────────────────────────────────────────────────
        let cart = [];
        let paymentMethod = 'cash';
        let lastSaleData = null;

        // ── DOM REFS ────────────────────────────────────────────────
        const $barcodeInput = document.getElementById('barcodeInput');
        const $productGrid = document.getElementById('product-grid');
        const $cartItemsList = document.getElementById('cart-items-list');
        const $cartEmpty = document.getElementById('cart-empty');
        const $cartCount = document.getElementById('cart-count');
        const $discountInput = document.getElementById('discountInput');
        const $btnCheckout = document.getElementById('btn-checkout');
        const $btnHoldOrder = document.getElementById('btn-hold-order');
        const $dispSubtotal = document.getElementById('disp-subtotal');
        const $dispTax = document.getElementById('disp-tax');
        const $dispTotal = document.getElementById('disp-total');
        const $taxRow = document.getElementById('tax-row');
        const $modalOverlay = document.getElementById('modal-overlay');
        const $mSubtotal = document.getElementById('m-subtotal');
        const $mTax = document.getElementById('m-tax');
        const $mTaxRow = document.getElementById('m-tax-row');
        const $mDiscount = document.getElementById('m-discount');
        const $mDiscRow = document.getElementById('m-disc-row');
        const $mTotal = document.getElementById('m-total');
        const $receivedAmount = document.getElementById('receivedAmount');
        const $changeAmount = document.getElementById('change-amount');
        const $changeLabel = document.getElementById('change-label');
        const $btnConfirmSale = document.getElementById('btn-confirm-sale');
        const $successOverlay = document.getElementById('success-overlay');
        const $successInvoice = document.getElementById('success-invoice-no');
        const $successChange = document.getElementById('success-change');
        const $quickCashBtns = document.getElementById('quick-cash-btns');
        const $priceMismatch = document.getElementById('price-mismatch-warn');
        const $priceMismatchMsg = document.getElementById('price-mismatch-msg');
        const $customerSelect = document.getElementById('customerSelect');
        const $heldBadge = document.getElementById('held-badge');

        // ── THEME ───────────────────────────────────────────────────
        const $html = document.documentElement;
        const $themeIcon = document.getElementById('theme-icon');
        const $themeLabel = document.getElementById('theme-label');

        function applyTheme(t) {
            $html.dataset.theme = t;
            $themeIcon.className = t === 'light' ? 'ti ti-moon' : 'ti ti-sun';
            $themeLabel.textContent = t === 'light' ? 'Dark' : 'Light';
        }
        applyTheme(localStorage.getItem('pos-theme') || 'dark');
        document.getElementById('theme-toggle').addEventListener('click', () => {
            const next = $html.dataset.theme === 'dark' ? 'light' : 'dark';
            applyTheme(next);
            localStorage.setItem('pos-theme', next);
        });

        // ── CLOCK ───────────────────────────────────────────────────
        const $clock = document.getElementById('pos-clock');

        function tick() {
            $clock.textContent = new Date().toLocaleTimeString('en-BD', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
        }
        tick();
        setInterval(tick, 1000);

        // ── TOAST ───────────────────────────────────────────────────
        function toast(msg, type = 'success', ms = 3500) {
            if (window.showSmartToast) {
                const titleMap = {
                    success: 'Success',
                    error: 'Error',
                    warning: 'Warning',
                    info: 'Information'
                };
                window.showSmartToast(type, msg, titleMap[type] || 'Notification');
            }
        }

        // ── PRODUCT GRID ────────────────────────────────────────────
        function renderProductGrid(products) {
            document.getElementById('grid-loading')?.remove();
            $productGrid.querySelectorAll('.prod-card,#grid-empty').forEach(el => el.remove());

            if (!products?.length) {
                $productGrid.insertAdjacentHTML('beforeend',
                    `<div id="grid-empty" role="status">
                        <i class="ti ti-package-off" style="font-size:40px;opacity:.3;display:block;margin-bottom:10px;" aria-hidden="true"></i>
                        <div>No products found for this selection.</div>
                    </div>`);
                return;
            }

            const frag = document.createDocumentFragment();
            products.forEach(p => {
                const card = document.createElement('div');
                const st = p.stock_status;
                card.className = `prod-card${st === 'out' ? ' out-of-stock' : st === 'low' ? ' low-stock' : ''}`;
                card.dataset.variantId = p.variant_id;
                card.setAttribute('role', 'listitem');
                card.setAttribute('tabindex', st !== 'out' ? '0' : '-1');
                card.setAttribute('aria-label', `${p.name}, ৳${fmt(p.price)}, stock: ${p.available_stock}`);

                const badgeCls = st === 'out' ? 'out' : st === 'low' ? 'low' : 'ok';
                const badgeTxt = st === 'out' ? 'Out' : st === 'low' ? 'Low' : 'In Stock';
                const imgHtml = p.image ?
                    `<img src="${p.image}" alt="${esc(p.short_name)}" class="prod-img" loading="lazy"
                           onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                       <div class="prod-img-ph" style="display:none;" aria-hidden="true"><i class="ti ti-package"></i></div>` :
                    `<div class="prod-img-ph" aria-hidden="true"><i class="ti ti-package"></i></div>`;

                card.innerHTML = `
                    <span class="stock-badge ${badgeCls}" aria-label="Stock: ${badgeTxt}">${badgeTxt}</span>
                    ${imgHtml}
                    <div class="prod-name">${esc(p.name)}</div>
                    <div class="prod-price">৳${fmt(p.price)}</div>
                    <div class="prod-stock">Stock: ${p.available_stock}</div>`;

                if (st !== 'out') {
                    card.addEventListener('click', () => addToCart(p));
                    card.addEventListener('keydown', e => {
                        if (e.key === 'Enter' || e.key === ' ') {
                            e.preventDefault();
                            addToCart(p);
                        }
                    });
                }
                frag.appendChild(card);
            });
            $productGrid.appendChild(frag);
        }
        renderProductGrid(INITIAL_PRODUCTS);

        // ── CATEGORY FILTER ─────────────────────────────────────────
        let activeCategory = '',
            gridSearch = '',
            fetchTimer = null;

        document.getElementById('category-bar').addEventListener('click', e => {
            const btn = e.target.closest('.cat-btn');
            if (!btn) return;
            document.querySelectorAll('.cat-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            activeCategory = btn.dataset.cat;
            fetchGrid();
        });

        // ── BARCODE / SEARCH ────────────────────────────────────────
        $barcodeInput.addEventListener('input', function() {
            clearTimeout(fetchTimer);
            gridSearch = this.value.trim();
            if (gridSearch.length >= 2) fetchTimer = setTimeout(fetchGrid, 300);
            else if (gridSearch.length === 0) fetchGrid();
        });

        $barcodeInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const q = this.value.trim();
                if (q) {
                    searchAndAdd(q);
                    this.value = '';
                    gridSearch = '';
                }
            }
            if (e.key === 'Escape') {
                this.value = '';
                gridSearch = '';
                fetchGrid();
            }
        });

        window.addEventListener('DOMContentLoaded', () => $barcodeInput.focus());

        document.addEventListener('click', e => {
            const anyModalOpen = $modalOverlay.classList.contains('open') ||
                $successOverlay.classList.contains('open') ||
                document.getElementById('held-panel-overlay').classList.contains('open') ||
                document.getElementById('add-customer-overlay').classList.contains('open') ||
                document.getElementById('help-overlay').classList.contains('open') ||
                document.getElementById('hold-label-overlay').classList.contains('open');
            if (!anyModalOpen &&
                !e.target.closest('#cart-panel') &&
                !e.target.closest('#category-bar') &&
                !e.target.closest('#theme-toggle')) {
                $barcodeInput.focus();
            }
        });

        // ── FETCH GRID (AJAX) ───────────────────────────────────────
        function fetchGrid() {
            const p = new URLSearchParams();
            if (activeCategory) p.set('category_id', activeCategory);
            if (gridSearch) p.set('q', gridSearch);

            $productGrid.querySelectorAll('.prod-card,#grid-empty').forEach(el => el.remove());
            $productGrid.insertAdjacentHTML('beforeend',
                `<div id="grid-loading" style="grid-column:1/-1;text-align:center;padding:60px 20px;color:var(--text-3);" role="status" aria-live="polite">
                    <div class="spin" aria-hidden="true"></div>
                    <div style="font-size:13px;">Loading…</div>
                </div>`);

            fetch(`${ROUTES.products}?${p}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(r => r.json())
                .then(d => {
                    document.getElementById('grid-loading')?.remove();
                    if (d.success) renderProductGrid(d.products);
                })
                .catch(() => {
                    document.getElementById('grid-loading')?.remove();
                    toast('Failed to load products.', 'error');
                });
        }

        // ── SEARCH & ADD (BARCODE ENTER) ────────────────────────────
        function searchAndAdd(query) {
            fetch(`${ROUTES.search}?q=${encodeURIComponent(query)}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(r => r.json())
                .then(d => {
                    if (d.success) {
                        addToCart(d);
                        toast(`Added: ${d.name}`, 'success', 2000);
                    } else toast(d.message || 'Product not found.', 'error');
                })
                .catch(() => toast('Search failed. Check your connection.', 'error'));
        }

        // ── CART OPERATIONS ─────────────────────────────────────────
        function addToCart(p) {
            if (p.stock_status === 'out' || p.available_stock <= 0) {
                toast(`"${p.name}" is out of stock.`, 'warning');
                return;
            }
            const ex = cart.find(i => i.variant_id === p.variant_id);
            if (ex) {
                if (ex.qty >= p.available_stock) {
                    toast(`Max stock reached for "${p.name}" (${p.available_stock} available).`, 'warning');
                    return;
                }
                ex.qty++;
            } else {
                cart.push({
                    variant_id: p.variant_id,
                    name: p.name,
                    price: parseFloat(p.price),
                    qty: 1,
                    tax_rate: parseFloat(p.tax_rate || 0),
                    available_stock: parseInt(p.available_stock),
                    reorder_level: parseInt(p.reorder_level || 5),
                });
            }
            renderCart();
            bumpCount();
        }

        function updateQty(vid, delta) {
            const item = cart.find(i => i.variant_id === vid);
            if (!item) return;
            const nq = item.qty + delta;
            if (nq <= 0) {
                removeFromCart(vid);
                return;
            }
            if (nq > item.available_stock) {
                toast(`Only ${item.available_stock} units available.`, 'warning');
                return;
            }
            item.qty = nq;
            renderCart();
        }

        function removeFromCart(vid) {
            cart = cart.filter(i => i.variant_id !== vid);
            renderCart();
        }

        function clearCart() {
            if (!cart.length) return;
            cart = [];
            renderCart();
            toast('Cart cleared.', 'warning', 2000);
        }

        // ── CART RENDER ─────────────────────────────────────────────
        function renderCart() {
            const empty = !cart.length;
            $cartEmpty.style.display = empty ? 'flex' : 'none';
            $cartItemsList.style.display = empty ? 'none' : 'block';
            $cartCount.textContent = cart.reduce((s, i) => s + i.qty, 0);
            $btnHoldOrder.disabled = empty;

            if (empty) {
                $cartItemsList.innerHTML = '';
                updateTotals();
                return;
            }

            // Use event delegation — no inline onclick (avoids XSS surface)
            $cartItemsList.innerHTML = cart.map(item => {
                const lineTotal = round2(item.price * item.qty);
                const warn = item.qty >= item.available_stock ?
                    `<span class="stock-warn"><i class="ti ti-alert-triangle" aria-hidden="true"></i> Only ${item.available_stock} left</span>` :
                    '';
                return `
                <div class="cart-item" data-vid="${item.variant_id}" role="listitem">
                    <div class="item-name">${esc(item.name)}</div>
                    <div class="item-total">৳${fmt(lineTotal)}</div>
                    <div class="item-unit">৳${fmt(item.price)} each${item.tax_rate > 0 ? ` · ${item.tax_rate}% tax` : ''}</div>
                    <div class="item-controls">
                        <button class="qty-btn" data-action="dec" data-vid="${item.variant_id}" aria-label="Decrease quantity">−</button>
                        <span class="qty-disp" aria-label="Quantity: ${item.qty}">${item.qty}</span>
                        <button class="qty-btn" data-action="inc" data-vid="${item.variant_id}" aria-label="Increase quantity">+</button>
                        ${warn}
                        <button class="item-remove" data-action="remove" data-vid="${item.variant_id}" title="Remove" aria-label="Remove ${esc(item.name)} from cart">
                            <i class="ti ti-trash" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>`;
            }).join('');

            updateTotals();
        }

        // Event delegation for cart item controls (no inline onclick)
        $cartItemsList.addEventListener('click', e => {
            const btn = e.target.closest('[data-action]');
            if (!btn) return;
            const vid = parseInt(btn.dataset.vid);
            const action = btn.dataset.action;
            if (action === 'dec') updateQty(vid, -1);
            if (action === 'inc') updateQty(vid, 1);
            if (action === 'remove') removeFromCart(vid);
        });

        // ── TOTALS (per-line rounding — mirrors backend) ─────────────
        function round2(n) {
            return Math.round(n * 100) / 100;
        }

        function calcTotals() {
            let sub = 0,
                tax = 0;
            cart.forEach(i => {
                const lineSubtotal = round2(i.price * i.qty);
                const lineTax = round2(lineSubtotal * (i.tax_rate / 100));
                sub += lineSubtotal;
                tax += lineTax;
            });
            sub = round2(sub);
            tax = round2(tax);
            const disc = Math.max(0, parseFloat($discountInput.value) || 0);
            const total = Math.max(0, round2(sub + tax - disc));
            return {
                sub,
                tax,
                disc,
                total
            };
        }

        function updateTotals() {
            const {
                sub,
                tax,
                disc,
                total
            } = calcTotals();
            $dispSubtotal.textContent = fmt(sub);
            $dispTax.textContent = fmt(tax);
            $dispTotal.textContent = fmt(total);
            $taxRow.style.display = tax > 0 ? 'flex' : 'none';
            $btnCheckout.disabled = !cart.length;
        }
        $discountInput.addEventListener('input', updateTotals);

        function bumpCount() {
            $cartCount.style.transform = 'scale(1.4)';
            $cartCount.style.transition = 'transform .2s';
            setTimeout(() => $cartCount.style.transform = 'scale(1)', 150);
        }

        // ── CLEAR CART ───────────────────────────────────────────────
        document.getElementById('btn-clear-cart').addEventListener('click', clearCart);

        // ── CHECKOUT MODAL ───────────────────────────────────────────
        $btnCheckout.addEventListener('click', openModal);
        document.getElementById('btn-close-modal').addEventListener('click', closeModal);
        $modalOverlay.addEventListener('click', e => {
            if (e.target === $modalOverlay) closeModal();
        });

        function openModal() {
            if (!cart.length) return;
            const {
                sub,
                tax,
                disc,
                total
            } = calcTotals();
            $mSubtotal.textContent = fmt(sub);
            $mTax.textContent = fmt(tax);
            $mDiscount.textContent = fmt(disc);
            $mTotal.textContent = fmt(total);
            $mTaxRow.style.display = tax > 0 ? 'flex' : 'none';
            $mDiscRow.style.display = disc > 0 ? 'flex' : 'none';
            $receivedAmount.value = total.toFixed(2);
            $priceMismatch.style.display = 'none';
            updateChange();
            buildQuickCash(total);
            $modalOverlay.classList.add('open');
            setTimeout(() => $receivedAmount.focus(), 100);
        }

        function closeModal() {
            $modalOverlay.classList.remove('open');
            $barcodeInput.focus();
        }

        // ── PAYMENT METHOD ───────────────────────────────────────────
        document.querySelectorAll('.pay-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.pay-btn').forEach(b => {
                    b.classList.remove('selected');
                    b.setAttribute('aria-pressed', 'false');
                });
                this.classList.add('selected');
                this.setAttribute('aria-pressed', 'true');
                paymentMethod = this.dataset.method;
                updateChange();
            });
            btn.addEventListener('keydown', e => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    btn.click();
                }
            });
        });

        // ── CHANGE CALC (fixed: shows "Amount Due" when negative) ────
        $receivedAmount.addEventListener('input', updateChange);

        function updateChange() {
            const {
                total
            } = calcTotals();
            const recv = parseFloat($receivedAmount.value) || 0;
            const change = round2(recv - total);

            if (change < 0) {
                $changeLabel.innerHTML = '<i class="ti ti-alert-circle" aria-hidden="true"></i> Amount Due';
                $changeAmount.textContent = '৳' + fmt(Math.abs(change));
                $changeAmount.className = 'negative';
            } else {
                $changeLabel.innerHTML = '<i class="ti ti-coins" aria-hidden="true"></i> Change to Return';
                $changeAmount.textContent = '৳' + fmt(change);
                $changeAmount.className = '';
            }

            $btnConfirmSale.disabled = recv < total || !cart.length;
        }

        // ── QUICK CASH ───────────────────────────────────────────────
        function buildQuickCash(total) {
            // Use sensible denominations: exact total + common round-ups
            const denoms = [1, 5, 10, 20, 50, 100, 200, 500, 1000];
            const seen = new Set();
            const vals = [];
            // Always include exact total
            vals.push(round2(total));
            seen.add(round2(total));
            // Add round-up denominations
            for (const d of denoms) {
                const v = Math.ceil(total / d) * d;
                if (v >= total && !seen.has(v)) {
                    seen.add(v);
                    vals.push(v);
                }
                if (vals.length >= 5) break;
            }
            vals.sort((a, b) => a - b);
            $quickCashBtns.innerHTML = vals.slice(0, 5).map(v =>
                `<button class="qc-btn" data-amt="${v}" aria-label="Set received amount to ৳${fmt(v)}">৳${fmt(v)}</button>`
            ).join('');
        }

        $quickCashBtns.addEventListener('click', e => {
            const btn = e.target.closest('.qc-btn');
            if (!btn) return;
            $receivedAmount.value = parseFloat(btn.dataset.amt).toFixed(2);
            updateChange();
        });

        // ── CONFIRM SALE ─────────────────────────────────────────────
        $btnConfirmSale.addEventListener('click', confirmSale);

        async function confirmSale() {
            if (!cart.length || $btnConfirmSale.disabled) return;
            const {
                total
            } = calcTotals();
            const recv = parseFloat($receivedAmount.value) || 0;
            if (recv < total) {
                toast('Received amount is less than total payable.', 'error');
                return;
            }

            $btnConfirmSale.classList.add('loading');
            $btnConfirmSale.disabled = true;
            $priceMismatch.style.display = 'none';

            // NOTE: We only send variant_id + qty. Server fetches authoritative price/tax.
            const payload = {
                items: cart.map(i => ({
                    variant_id: i.variant_id,
                    qty: i.qty,
                })),
                customer_id: $customerSelect.value || null,
                payment_method: paymentMethod,
                received_amount: recv,
                discount: parseFloat($discountInput.value) || 0,
            };

            try {
                const res = await fetch(ROUTES.checkout, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify(payload),
                });
                const data = await res.json();

                if (data.success) {
                    // Check if server total differs from client total (price change race)
                    if (data.server_total !== undefined && Math.abs(data.server_total - total) > 0.01) {
                        $priceMismatchMsg.textContent =
                            `Note: Server recalculated total as ৳${fmt(data.server_total)} (was ৳${fmt(total)}). Change adjusted.`;
                        $priceMismatch.style.display = 'flex';
                    }
                    lastSaleData = data;
                    closeModal();
                    showSuccess(data);
                    fetchGrid();
                } else {
                    // User-friendly error — raw server messages are already sanitised in controller
                    toast(data.message || 'Checkout failed. Please try again.', 'error', 6000);

                    // If stock error, offer recovery hint
                    if (data.message && data.message.toLowerCase().includes('insufficient stock')) {
                        toast('Tip: Reduce the quantity or remove the out-of-stock item.', 'info', 6000);
                    }
                }
            } catch {
                toast('Network error. Please check your connection and try again.', 'error', 6000);
            } finally {
                $btnConfirmSale.classList.remove('loading');
                $btnConfirmSale.disabled = false;
                updateChange(); // re-evaluate disabled state
            }
        }

        // ── SUCCESS OVERLAY ──────────────────────────────────────────
        function showSuccess(data) {
            $successInvoice.textContent = 'Invoice: ' + data.invoice_no;
            $successChange.textContent = '৳' + fmt(data.change >= 0 ? data.change : 0);
            $successOverlay.classList.add('open');
        }

        document.getElementById('btn-print-invoice').addEventListener('click', () => {
            if (lastSaleData?.print_url) window.open(lastSaleData.print_url, '_blank');
        });

        document.getElementById('btn-new-sale').addEventListener('click', startNewSale);

        function startNewSale() {
            $successOverlay.classList.remove('open');
            cart = [];
            $discountInput.value = '0';
            paymentMethod = 'cash';
            document.querySelectorAll('.pay-btn').forEach(b => {
                b.classList.remove('selected');
                b.setAttribute('aria-pressed', 'false');
            });
            document.querySelector('.pay-btn[data-method="cash"]').classList.add('selected');
            document.querySelector('.pay-btn[data-method="cash"]').setAttribute('aria-pressed', 'true');
            $customerSelect.value = '';
            renderCart();
            lastSaleData = null;
            $barcodeInput.focus();
            toast('Ready for new sale!', 'success', 2000);
        }

        // ── HOLD ORDER ───────────────────────────────────────────────
        const $holdLabelOverlay = document.getElementById('hold-label-overlay');
        const $holdLabelInput = document.getElementById('holdLabelInput');

        document.getElementById('btn-hold-order').addEventListener('click', () => {
            if (!cart.length) return;
            $holdLabelInput.value = '';
            $holdLabelOverlay.classList.add('open');
            setTimeout(() => $holdLabelInput.focus(), 100);
        });

        document.getElementById('btn-close-hold-label').addEventListener('click', () => {
            $holdLabelOverlay.classList.remove('open');
        });

        document.getElementById('btn-confirm-hold').addEventListener('click', async () => {
            if (!cart.length) return;
            const label = $holdLabelInput.value.trim();
            const {
                disc
            } = calcTotals();

            try {
                const res = await fetch(ROUTES.hold, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        items: cart,
                        customer_id: $customerSelect.value || null,
                        discount: disc,
                        label: label || null,
                    }),
                });
                const data = await res.json();
                if (data.success) {
                    $holdLabelOverlay.classList.remove('open');
                    clearCart();
                    toast(`Order held${label ? ': ' + label : ''}.`, 'success');
                    refreshHeldBadge();
                } else {
                    toast(data.message || 'Failed to hold order.', 'error');
                }
            } catch {
                toast('Network error while holding order.', 'error');
            }
        });

        // ── HELD ORDERS PANEL ────────────────────────────────────────
        const $heldPanelOverlay = document.getElementById('held-panel-overlay');
        const $heldList = document.getElementById('held-list');

        document.getElementById('btn-show-held').addEventListener('click', openHeldPanel);
        document.getElementById('btn-close-held').addEventListener('click', () => $heldPanelOverlay.classList.remove(
            'open'));
        $heldPanelOverlay.addEventListener('click', e => {
            if (e.target === $heldPanelOverlay) $heldPanelOverlay.classList.remove('open');
        });

        async function openHeldPanel() {
            $heldPanelOverlay.classList.add('open');
            $heldList.innerHTML =
                `<div style="text-align:center;padding:30px;color:var(--text-3);"><div class="spin" style="margin:0 auto 10px;" aria-hidden="true"></div>Loading…</div>`;
            try {
                const res = await fetch(ROUTES.heldOrders, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await res.json();
                renderHeldList(data.orders || []);
            } catch {
                $heldList.innerHTML =
                    `<div style="text-align:center;padding:30px;color:var(--danger);">Failed to load held orders.</div>`;
            }
        }

        function renderHeldList(orders) {
            if (!orders.length) {
                $heldList.innerHTML =
                    `<div id="held-empty" style="text-align:center;padding:40px 20px;color:var(--text-3);">
                    <i class="ti ti-stack-2" style="font-size:36px;opacity:.3;display:block;margin-bottom:8px;" aria-hidden="true"></i>No held orders.</div>`;
                return;
            }
            $heldList.innerHTML = orders.map(o => `
                <div class="held-card" data-id="${o.id}">
                    <div class="held-info">
                        <div class="held-label">${esc(o.label || 'Unnamed Order')}</div>
                        <div class="held-meta">${esc(o.customer)} · ${o.item_count} item${o.item_count !== 1 ? 's' : ''} · ${esc(o.updated_at)}</div>
                    </div>
                    <div class="held-actions">
                        <button class="held-recall-btn" data-id="${o.id}" data-order='${JSON.stringify(o)}' aria-label="Recall order ${esc(o.label || o.id)}">
                            <i class="ti ti-player-play" aria-hidden="true"></i> Recall
                        </button>
                        <button class="held-discard-btn" data-id="${o.id}" aria-label="Discard order ${esc(o.label || o.id)}">
                            <i class="ti ti-trash" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>`).join('');

            // Recall
            $heldList.querySelectorAll('.held-recall-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const order = JSON.parse(btn.dataset.order);
                    recallHeldOrder(order);
                });
            });

            // Discard
            $heldList.querySelectorAll('.held-discard-btn').forEach(btn => {
                btn.addEventListener('click', async () => {
                    if (!confirm('Discard this held order?')) return;
                    try {
                        const res = await fetch(`${ROUTES.deleteHeld}/${btn.dataset.id}`, {
                            method: 'DELETE',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': CSRF,
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                        });
                        const data = await res.json();
                        if (data.success) {
                            toast('Held order discarded.', 'warning');
                            openHeldPanel();
                            refreshHeldBadge();
                        } else toast(data.message || 'Failed to discard.', 'error');
                    } catch {
                        toast('Network error.', 'error');
                    }
                });
            });
        }

        function recallHeldOrder(order) {
            if (cart.length && !confirm('Recalling will replace your current cart. Continue?')) return;
            cart = (order.items || []).map(i => ({
                variant_id: i.variant_id,
                name: i.name,
                price: parseFloat(i.price),
                qty: parseInt(i.qty),
                tax_rate: parseFloat(i.tax_rate || 0),
                available_stock: parseInt(i.available_stock || 999),
                reorder_level: parseInt(i.reorder_level || 5),
            }));
            $discountInput.value = order.discount || 0;
            if (order.customer_id) $customerSelect.value = order.customer_id;
            renderCart();
            $heldPanelOverlay.classList.remove('open');
            toast('Order recalled!', 'success');
            // Delete the held order after recall
            fetch(`${ROUTES.deleteHeld}/${order.id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': CSRF,
                    'X-Requested-With': 'XMLHttpRequest'
                },
            }).then(() => refreshHeldBadge()).catch(() => {});
        }

        async function refreshHeldBadge() {
            try {
                const res = await fetch(ROUTES.heldOrders, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await res.json();
                const count = (data.orders || []).length;
                $heldBadge.textContent = count;
                $heldBadge.style.display = count > 0 ? 'inline-flex' : 'none';
            } catch {}
        }
        refreshHeldBadge();

        // ── QUICK-ADD CUSTOMER ───────────────────────────────────────
        const $addCustOverlay = document.getElementById('add-customer-overlay');
        const $addCustError = document.getElementById('add-cust-error');
        const $custSpin = document.getElementById('cust-spin');
        const $custBtnText = document.getElementById('cust-btn-text');

        document.getElementById('btn-add-customer').addEventListener('click', () => {
            document.getElementById('newCustName').value = '';
            document.getElementById('newCustPhone').value = '';
            document.getElementById('newCustEmail').value = '';
            $addCustError.style.display = 'none';
            $addCustOverlay.classList.add('open');
            setTimeout(() => document.getElementById('newCustName').focus(), 100);
        });

        document.getElementById('btn-close-add-customer').addEventListener('click', () => $addCustOverlay.classList.remove(
            'open'));
        $addCustOverlay.addEventListener('click', e => {
            if (e.target === $addCustOverlay) $addCustOverlay.classList.remove('open');
        });

        document.getElementById('btn-save-customer').addEventListener('click', async () => {
            const name = document.getElementById('newCustName').value.trim();
            const phone = document.getElementById('newCustPhone').value.trim();
            const email = document.getElementById('newCustEmail').value.trim();

            if (!name) {
                $addCustError.textContent = 'Customer name is required.';
                $addCustError.style.display = 'block';
                return;
            }
            $addCustError.style.display = 'none';

            $custSpin.style.display = 'block';
            $custBtnText.style.display = 'none';
            document.getElementById('btn-save-customer').disabled = true;

            try {
                const res = await fetch(ROUTES.quickCust, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        name,
                        phone: phone || null,
                        email: email || null
                    }),
                });
                const data = await res.json();
                if (data.success) {
                    // Add to dropdown and select
                    const opt = document.createElement('option');
                    opt.value = data.customer.id;
                    opt.textContent = data.customer.name + (data.customer.phone ? ' · ' + data.customer.phone :
                        '');
                    $customerSelect.appendChild(opt);
                    $customerSelect.value = data.customer.id;
                    $addCustOverlay.classList.remove('open');
                    toast(`Customer "${data.customer.name}" added and selected.`, 'success');
                } else {
                    $addCustError.textContent = data.message || 'Failed to create customer.';
                    $addCustError.style.display = 'block';
                }
            } catch {
                $addCustError.textContent = 'Network error. Please try again.';
                $addCustError.style.display = 'block';
            } finally {
                $custSpin.style.display = 'none';
                $custBtnText.style.display = 'inline-flex';
                document.getElementById('btn-save-customer').disabled = false;
            }
        });

        // ── KEYBOARD SHORTCUTS HELP ──────────────────────────────────
        const $helpOverlay = document.getElementById('help-overlay');
        document.getElementById('btn-help').addEventListener('click', () => $helpOverlay.classList.add('open'));
        document.getElementById('btn-close-help').addEventListener('click', () => $helpOverlay.classList.remove('open'));
        $helpOverlay.addEventListener('click', e => {
            if (e.target === $helpOverlay) $helpOverlay.classList.remove('open');
        });

        // ── KEYBOARD SHORTCUTS ───────────────────────────────────────
        document.addEventListener('keydown', e => {
            // Don't fire shortcuts when typing in an input/textarea
            const tag = document.activeElement?.tagName;
            const inInput = tag === 'INPUT' || tag === 'TEXTAREA' || tag === 'SELECT';

            if (e.key === 'F2') {
                e.preventDefault();
                $barcodeInput.focus();
                $barcodeInput.select();
            }
            if (e.key === 'F4' && cart.length) {
                e.preventDefault();
                openModal();
            }
            if (e.key === 'F6') {
                e.preventDefault();
                if (cart.length) document.getElementById('btn-hold-order').click();
                else openHeldPanel();
            }
            if (e.key === 'F8') {
                e.preventDefault();
                clearCart();
            }
            if (e.key === 'Escape') {
                closeModal();
                $successOverlay.classList.remove('open');
                $heldPanelOverlay.classList.remove('open');
                $addCustOverlay.classList.remove('open');
                $helpOverlay.classList.remove('open');
                $holdLabelOverlay.classList.remove('open');
            }
            if (e.key === '?' && !inInput) {
                e.preventDefault();
                $helpOverlay.classList.add('open');
            }
        });

        // ── UTILS ────────────────────────────────────────────────────
        function fmt(n) {
            return parseFloat(n || 0).toLocaleString('en-BD', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function esc(str) {
            const d = document.createElement('div');
            d.appendChild(document.createTextNode(str || ''));
            return d.innerHTML;
        }

        // ── INIT ─────────────────────────────────────────────────────
        renderCart();
        updateTotals();
    </script>

</body>

</html>
