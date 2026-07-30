<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $sale->invoice_no }}</title>
    <style>
        /* ── Reset ─────────────────────────────────────────────────── */
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        /* ── Screen: Centered preview card ─────────────────────────── */
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: #f0f2f5;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 24px 16px 40px;
            color: #1a1a2e;
        }

        /* ── Receipt Card ───────────────────────────────────────────── */
        .receipt {
            background: #fff;
            width: 100%;
            max-width: {{ $template && $template->type === 'a4' ? '794px' : '380px' }};
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, .12);
            overflow: hidden;
        }

        /* ── Header Band ────────────────────────────────────────────── */
        .receipt-header {
            background: linear-gradient(135deg, #1a1d27, #2e3250);
            color: #fff;
            padding: 20px 20px 16px;
            text-align: center;
        }

        .receipt-header .company-name {
            font-size: 20px;
            font-weight: 800;
            letter-spacing: .5px;
            margin-bottom: 3px;
        }

        .receipt-header .branch-name {
            font-size: 13px;
            color: #8b92b8;
            margin-bottom: 2px;
        }

        .receipt-header .contact {
            font-size: 12px;
            color: #6b7280;
        }

        /* ── Invoice Meta ───────────────────────────────────────────── */
        .receipt-meta {
            padding: 14px 20px;
            border-bottom: 1px dashed #e5e7eb;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px 12px;
        }

        .meta-item {
            font-size: 12px;
        }

        .meta-item .label {
            color: #9ca3af;
            display: block;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: .5px;
            margin-bottom: 1px;
        }

        .meta-item .value {
            color: #1a1a2e;
            font-weight: 600;
        }

        .meta-item.full {
            grid-column: 1 / -1;
        }

        /* ── Items Table ────────────────────────────────────────────── */
        .receipt-items {
            padding: 0 20px;
        }

        .items-header {
            display: grid;
            grid-template-columns: 1fr 50px 70px 70px;
            gap: 4px;
            padding: 10px 0 6px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #9ca3af;
        }

        .items-header .text-right,
        .item-row .text-right {
            text-align: right;
        }

        .item-row {
            display: grid;
            grid-template-columns: 1fr 50px 70px 70px;
            gap: 4px;
            padding: 8px 0;
            border-bottom: 1px dashed #f3f4f6;
            font-size: 13px;
            align-items: start;
        }

        .item-row:last-child {
            border-bottom: none;
        }

        .item-row .item-name {
            font-weight: 600;
            color: #1a1a2e;
            line-height: 1.3;
        }

        .item-row .item-sku {
            font-size: 10px;
            color: #9ca3af;
            margin-top: 1px;
        }

        .item-row .item-qty {
            color: #4b5563;
            text-align: right;
        }

        .item-row .item-price {
            color: #4b5563;
            text-align: right;
        }

        .item-row .item-total {
            font-weight: 700;
            color: #1a1a2e;
            text-align: right;
        }

        /* ── Totals ─────────────────────────────────────────────────── */
        .receipt-totals {
            padding: 12px 20px;
            border-top: 1px dashed #e5e7eb;
            background: #f9fafb;
        }

        .total-line {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
            color: #6b7280;
            padding: 3px 0;
        }

        .total-line .amount {
            font-weight: 600;
            color: #374151;
        }

        .total-line.discount .amount {
            color: #059669;
        }

        .total-line.tax .amount {
            color: #d97706;
        }

        .total-line.grand {
            font-size: 17px;
            font-weight: 800;
            color: #1a1a2e;
            border-top: 2px solid #1a1d27;
            margin-top: 8px;
            padding-top: 10px;
        }

        .total-line.grand .amount {
            color: #4f6ef7;
            font-size: 20px;
        }

        .total-line.paid {
            margin-top: 6px;
        }

        .total-line.change {
            color: #059669;
        }

        .total-line.change .amount {
            color: #059669;
            font-size: 15px;
        }

        /* Payment Badge */
        .payment-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: #eff6ff;
            color: #3b82f6;
            border: 1px solid #bfdbfe;
            border-radius: 20px;
            padding: 3px 10px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
            margin-top: 10px;
        }

        /* ── Footer ─────────────────────────────────────────────────── */
        .receipt-footer {
            padding: 14px 20px 18px;
            text-align: center;
            border-top: 1px dashed #e5e7eb;
        }

        .receipt-footer .thank-you {
            font-size: 14px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 4px;
        }

        .receipt-footer .tagline {
            font-size: 11px;
            color: #9ca3af;
        }

        .receipt-footer .barcode-area {
            margin-top: 12px;
            font-family: 'Courier New', monospace;
            font-size: 11px;
            color: #6b7280;
            letter-spacing: 2px;
        }

        /* ── Custom HTML template content ───────────────────────────── */
        .custom-template-body {
            padding: 20px;
        }

        /* ── Action Buttons (screen only) ───────────────────────────── */
        .print-actions {
            margin-top: 20px;
            display: flex;
            gap: 10px;
            width: 100%;
            max-width: {{ $template && $template->type === 'a4' ? '794px' : '380px' }};
        }

        .btn-action {
            flex: 1;
            padding: 12px;
            border-radius: 9px;
            border: none;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all .2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            text-decoration: none;
        }

        .btn-print {
            background: #1a1d27;
            color: #fff;
        }

        .btn-print:hover {
            background: #2e3250;
        }

        .btn-new-sale {
            background: #4f6ef7;
            color: #fff;
        }

        .btn-new-sale:hover {
            background: #3d5ce0;
        }

        .btn-sales {
            background: #f3f4f6;
            color: #374151;
            border: 1px solid #e5e7eb;
        }

        .btn-sales:hover {
            background: #e5e7eb;
        }

        /* ── Print Media ────────────────────────────────────────────── */
        @media print {
            body {
                background: #fff;
                padding: 0;
                display: block;
            }

            .receipt {
                max-width: {{ $template && $template->type === 'a4' ? '210mm' : '80mm' }};
                width: {{ $template && $template->type === 'a4' ? '210mm' : '80mm' }};
                border-radius: 0;
                box-shadow: none;
                margin: 0;
            }

            .print-actions {
                display: none !important;
            }

            .receipt-header {
                background: #000 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .receipt-totals {
                background: #f9f9f9 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            @page {
                margin: 4mm;
                size: {{ $template && $template->type === 'a4' ? 'A4' : '80mm auto' }};
            }
        }
    </style>

    {{-- Inject any extra CSS from template settings --}}
    @if ($template && !empty($template->settings['custom_css']))
        <style>
            {{ $template->settings['custom_css'] }}
        </style>
    @endif
</head>

<body>

    {{-- ── RECEIPT CARD ──────────────────────────────────────────────── --}}
    <div class="receipt">

        {{-- If template has custom HTML content, render it with variable substitution --}}
        @if ($template && !empty($template->html_content))
            @php
                $company = $sale->branch->company ?? auth()->user()->company;
                $branch = $sale->branch ?? auth()->user()->branch;
                $customer = $sale->customer;

                // Build items HTML for injection
                $itemsHtml = '';
                foreach ($sale->items as $item) {
                    $itemsHtml .=
                        '<tr>' .
                        '<td>' .
                        e($item->product_name) .
                        '</td>' .
                        '<td style="text-align:right">' .
                        $item->quantity .
                        '</td>' .
                        '<td style="text-align:right">৳' .
                        number_format($item->unit_price, 2) .
                        '</td>' .
                        '<td style="text-align:right">৳' .
                        number_format($item->subtotal, 2) .
                        '</td>' .
                        '</tr>';
                }

                $taxAmount = $sale->total_amount - $sale->subtotal + $sale->discount;

                $vars = [
                    'company_name' => e($company->name ?? ''),
                    'branch_name' => e($branch->name ?? ''),
                    'branch_phone' => e($branch->phone ?? ''),
                    'branch_address' => e($branch->address ?? ''),
                    'invoice_no' => e($sale->invoice_no),
                    'sale_date' => $sale->created_at->format('d M Y, h:i A'),
                    'cashier_name' => e($sale->user->name ?? 'Staff'),
                    'customer_name' => e($customer->name ?? 'Walk-in Customer'),
                    'items_table' => $itemsHtml,
                    'subtotal' => '৳' . number_format($sale->subtotal, 2),
                    'discount' => '৳' . number_format($sale->discount, 2),
                    'tax_amount' => '৳' . number_format($taxAmount, 2),
                    'total_amount' => '৳' . number_format($sale->total_amount, 2),
                    'received_amount' => '৳' . number_format($sale->received_amount, 2),
                    'change_amount' => '৳' . number_format(max(0, $sale->received_amount - $sale->total_amount), 2),
                    'payment_method' => ucfirst(str_replace('_', ' ', $sale->payment_method)),
                ];

                $html = $template->html_content;
                $ob = '{';
                $cb = '}';
                foreach ($vars as $key => $value) {
                    // Replace {{ key }} and {{ key }} placeholder styles
                    $html = str_replace($ob . $ob . $key . $cb . $cb, $value, $html);
                    $html = str_replace($ob . $ob . ' ' . $key . ' ' . $cb . $cb, $value, $html);
                }
            @endphp
            <div class="custom-template-body">{!! $html !!}</div>
        @else
            {{-- ── DEFAULT BUILT-IN RECEIPT LAYOUT ──────────────────── --}}

            {{-- Header --}}
            <div class="receipt-header">
                <div class="company-name">
                    {{ $sale->branch->company->name ?? (auth()->user()->company->name ?? 'Company Name') }}
                </div>
                <div class="branch-name">
                    {{ $sale->branch->name ?? (auth()->user()->branch->name ?? 'Branch') }}
                </div>
                @if ($sale->branch->phone ?? null)
                    <div class="contact">📞 {{ $sale->branch->phone }}</div>
                @endif
                @if ($sale->branch->address ?? null)
                    <div class="contact">{{ $sale->branch->address }}</div>
                @endif
            </div>

            {{-- Invoice Meta --}}
            <div class="receipt-meta">
                <div class="meta-item">
                    <span class="label">Invoice No</span>
                    <span class="value">{{ $sale->invoice_no }}</span>
                </div>
                <div class="meta-item">
                    <span class="label">Date & Time</span>
                    <span class="value">{{ $sale->created_at->format('d M Y, h:i A') }}</span>
                </div>
                <div class="meta-item">
                    <span class="label">Cashier</span>
                    <span class="value">{{ $sale->user->name ?? 'Staff' }}</span>
                </div>
                <div class="meta-item">
                    <span class="label">Customer</span>
                    <span class="value">{{ $sale->customer->name ?? 'Walk-in Customer' }}</span>
                </div>
                @if ($template && !empty($template->settings['show_template_name']))
                    <div class="meta-item full" style="font-size:9px;color:#d1d5db;text-align:right;">
                        Template: {{ $template->name }}
                    </div>
                @endif
            </div>

            {{-- Items --}}
            <div class="receipt-items">
                <div class="items-header">
                    <span>Item</span>
                    <span class="text-right">Qty</span>
                    <span class="text-right">Price</span>
                    <span class="text-right">Total</span>
                </div>

                @foreach ($sale->items as $item)
                    <div class="item-row">
                        <div>
                            <div class="item-name">{{ $item->product_name }}</div>
                            @if ($item->variant?->sku)
                                <div class="item-sku">{{ $item->variant->sku }}</div>
                            @endif
                        </div>
                        <div class="item-qty">{{ $item->quantity }}</div>
                        <div class="item-price">৳{{ number_format($item->unit_price, 2) }}</div>
                        <div class="item-total">৳{{ number_format($item->subtotal, 2) }}</div>
                    </div>
                @endforeach
            </div>

            {{-- Totals --}}
            <div class="receipt-totals">
                <div class="total-line">
                    <span>Subtotal ({{ $sale->items->count() }} item{{ $sale->items->count() > 1 ? 's' : '' }})</span>
                    <span class="amount">৳{{ number_format($sale->subtotal, 2) }}</span>
                </div>

                @php $taxAmount = $sale->total_amount - $sale->subtotal + $sale->discount; @endphp
                @if ($taxAmount > 0)
                    <div class="total-line tax">
                        <span>Tax / VAT</span>
                        <span class="amount">৳{{ number_format($taxAmount, 2) }}</span>
                    </div>
                @endif

                @if ($sale->discount > 0)
                    <div class="total-line discount">
                        <span>Discount</span>
                        <span class="amount">-৳{{ number_format($sale->discount, 2) }}</span>
                    </div>
                @endif

                <div class="total-line grand">
                    <span>TOTAL</span>
                    <span class="amount">৳{{ number_format($sale->total_amount, 2) }}</span>
                </div>

                <div class="total-line paid">
                    <span>Paid ({{ ucfirst(str_replace('_', ' ', $sale->payment_method)) }})</span>
                    <span class="amount">৳{{ number_format($sale->received_amount, 2) }}</span>
                </div>

                @php $change = $sale->received_amount - $sale->total_amount; @endphp
                @if ($change > 0)
                    <div class="total-line change">
                        <span>Change Returned</span>
                        <span class="amount">৳{{ number_format($change, 2) }}</span>
                    </div>
                @endif

                <div style="text-align:center; margin-top: 10px;">
                    <span class="payment-badge">
                        ✓ {{ strtoupper(str_replace('_', ' ', $sale->payment_method)) }} PAID
                    </span>
                </div>
            </div>

            {{-- Footer --}}
            <div class="receipt-footer">
                @if ($template && !empty($template->settings['footer_text']))
                    <div class="thank-you">{{ $template->settings['footer_text'] }}</div>
                @else
                    <div class="thank-you">★ Thank You for Shopping! ★</div>
                    <div class="tagline">Goods once sold will not be taken back.</div>
                @endif
                @if (!($template && isset($template->settings['hide_invoice_barcode']) && $template->settings['hide_invoice_barcode']))
                    <div class="barcode-area">{{ $sale->invoice_no }}</div>
                @endif
            </div>

        @endif

    </div>{{-- /.receipt --}}

    {{-- ── ACTION BUTTONS (screen only) ─────────────────────────────── --}}
    <div class="print-actions">
        <button class="btn-action btn-print" onclick="window.print()">
            🖨️ Print Receipt
        </button>
        <a href="{{ route('branch.pos.index') }}" class="btn-action btn-new-sale">
            ＋ New Sale
        </a>
        <a href="{{ route('branch.sales.index') }}" class="btn-action btn-sales">
            📋 Sales Log
        </a>
    </div>

    <script>
        window.addEventListener('load', () => {
            setTimeout(() => window.print(), 400);
        });
    </script>

</body>

</html>
