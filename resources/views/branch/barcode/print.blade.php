<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barcode Labels</title>
    @php
        // Resolve settings from BarcodeSetting model (passed from controller)
        // Falls back to sensible defaults if no setting is configured yet.
        $bs = $barcodeSetting;
        $labelWidth = $bs ? (int) $bs->width : 150;
        $labelHeight = $bs ? (int) $bs->height : 80;
        $labelsPerRow = $bs ? (int) $bs->labels_per_row : 4;
        $showText = $bs ? (bool) $bs->show_text : true;
        $showPrice = $bs ? (bool) $bs->show_price : true;
        $showProductName = $bs ? (bool) $bs->show_product_name : true;
        $showCompanyName = $bs ? (bool) $bs->show_company_name : false;
        $barcodeType = $bs ? $bs->barcode_type : 'CODE128';
        $settingName = $bs ? $bs->name : 'Default';

        // Compute label grid column width as percentage
        $colWidth = floor(100 / $labelsPerRow);
    @endphp
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: white;
        }

        .label-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
            padding: 10px;
        }

        .label {
            width: {{ $labelWidth }}px;
            height: {{ $labelHeight }}px;
            border: 1px solid #ccc;
            padding: 4px 6px;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            page-break-inside: avoid;
            overflow: hidden;
        }

        .label .company-name {
            font-size: 7px;
            color: #555;
            margin-bottom: 2px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            max-width: {{ $labelWidth - 10 }}px;
        }

        .label .product-name {
            font-size: 9px;
            font-weight: bold;
            margin-bottom: 3px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            max-width: {{ $labelWidth - 10 }}px;
        }

        .label .barcode-text {
            font-family: 'Courier New', monospace;
            font-size: 10px;
            letter-spacing: 1px;
            margin-bottom: 1px;
        }

        .label .sku {
            font-size: 8px;
            color: #666;
        }

        .label .price {
            font-size: 9px;
            font-weight: bold;
            color: #1a1a2e;
            margin-top: 2px;
        }

        /* ── Barcode bars visual representation ─────────────────────── */
        .barcode-bars {
            display: flex;
            align-items: flex-end;
            height: {{ max(20, (int) ($labelHeight * 0.35)) }}px;
            gap: 1px;
            margin: 2px 0;
        }

        .bar {
            background: #000;
            width: 2px;
        }

        /* ── QR code placeholder (when type = QR) ───────────────────── */
        .qr-placeholder {
            width: {{ min(50, $labelHeight - 20) }}px;
            height: {{ min(50, $labelHeight - 20) }}px;
            border: 2px solid #000;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 7px;
            color: #666;
            margin: 2px 0;
        }

        /* ── No-print controls ──────────────────────────────────────── */
        .no-print {
            display: block;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                margin: 0;
            }

            .label-grid {
                padding: 5px;
            }

            .label {
                border: 1px solid #999;
            }
        }
    </style>
</head>

<body>

    {{-- ── Print Controls (screen only) ──────────────────────────────── --}}
    <div class="no-print"
        style="padding:15px; background:#f8f9fa; border-bottom:1px solid #dee2e6; margin-bottom:10px; display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
        <button onclick="window.print()"
            style="background:#0d6efd;color:white;border:none;padding:8px 20px;border-radius:4px;cursor:pointer;font-size:14px;">
            🖨️ Print Labels
        </button>
        <button onclick="window.close()"
            style="background:#6c757d;color:white;border:none;padding:8px 20px;border-radius:4px;cursor:pointer;font-size:14px;">
            ✕ Close
        </button>
        <span style="color:#666;font-size:13px;">
            {{ $variants->count() }} product(s) × {{ $copies }} cop{{ $copies > 1 ? 'ies' : 'y' }} =
            <strong>{{ $variants->count() * $copies }}</strong> labels
        </span>
        {{-- Show active setting info --}}
        <span
            style="margin-left:auto;background:#e9ecef;padding:4px 10px;border-radius:4px;font-size:12px;color:#495057;">
            ⚙️ Setting: <strong>{{ $settingName }}</strong>
            &nbsp;|&nbsp; Type: <strong>{{ $barcodeType }}</strong>
            &nbsp;|&nbsp; Size: <strong>{{ $labelWidth }}×{{ $labelHeight }}px</strong>
            &nbsp;|&nbsp; Per Row: <strong>{{ $labelsPerRow }}</strong>
        </span>
    </div>

    {{-- ── Label Grid ──────────────────────────────────────────────────── --}}
    <div class="label-grid">
        @foreach ($variants as $variant)
            @for ($i = 0; $i < $copies; $i++)
                @php
                    $code = $variant->barcode ?? ($variant->sku ?? str_pad($variant->id, 8, '0', STR_PAD_LEFT));
                    $productName = optional($variant->product)->name ?? 'Product';
                    $companyName = optional(optional($variant->product)->company)->name ?? '';
                    $price = optional($variant->product)->selling_price ?? null;
                    // Deterministic bar heights from code characters
                    $heights = [20, 35, 25, 40, 30, 35, 20, 40, 25, 30, 35, 20, 40, 25, 30];
                @endphp
                <div class="label">

                    {{-- Company name (if enabled) --}}
                    @if ($showCompanyName && $companyName)
                        <div class="company-name">{{ $companyName }}</div>
                    @endif

                    {{-- Product name (if enabled) --}}
                    @if ($showProductName)
                        <div class="product-name">{{ $productName }}</div>
                        @if ($variant->name)
                            <div class="sku" style="font-size:7px;margin-bottom:2px;">{{ $variant->name }}</div>
                        @endif
                    @endif

                    {{-- Barcode visual --}}
                    @if ($barcodeType === 'QR')
                        {{-- QR placeholder — real QR requires a JS/PHP library --}}
                        <div class="qr-placeholder">QR<br>{{ Str::limit($code, 6) }}</div>
                    @else
                        <div class="barcode-bars">
                            @foreach (str_split($code) as $idx => $char)
                                <div class="bar"
                                    style="height:{{ $heights[$idx % 15] }}px;width:{{ ord($char) % 2 === 0 ? '1px' : '2px' }};">
                                </div>
                                @if ($idx % 3 === 2)
                                    <div style="width:1px;"></div>
                                @endif
                            @endforeach
                        </div>
                    @endif

                    {{-- Barcode text (if enabled) --}}
                    @if ($showText)
                        <div class="barcode-text">{{ $code }}</div>
                    @endif

                    {{-- Price (if enabled) --}}
                    @if ($showPrice && $price)
                        <div class="price">৳{{ number_format($price, 2) }}</div>
                    @endif

                </div>
            @endfor
        @endforeach
    </div>

    <script>
        // Auto-print on load
        window.onload = function() {
            setTimeout(() => window.print(), 500);
        };
    </script>
</body>

</html>
