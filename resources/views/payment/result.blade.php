<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        @if ($status === 'success')
            ✅ Payment Successful
        @elseif($status === 'failed')
            ❌ Payment Failed
        @elseif($status === 'cancelled')
            ⚠️ Payment Cancelled
        @else
            ⚠️ Payment Error
        @endif
        — {{ config('app.name') }}
    </title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.19.0/dist/tabler-icons.min.css">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            position: relative;
            overflow-x: hidden;
        }

        /* ── Animated gradient background ── */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            z-index: -1;

            @if ($status === 'success')
                background: linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%);
            @elseif($status === 'failed')
                background: linear-gradient(135deg, #1a0a0a 0%, #2d1515 50%, #3d1a1a 100%);
            @elseif($status === 'cancelled')
                background: linear-gradient(135deg, #1a1500 0%, #2d2500 50%, #3d3200 100%);
            @else
                background: linear-gradient(135deg, #0a0a1a 0%, #151530 50%, #1a1a40 100%);
            @endif
        }

        /* ── Floating orbs ── */
        .orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.15;
            animation: float 8s ease-in-out infinite;
            z-index: -1;
        }

        .orb-1 {
            width: 400px;
            height: 400px;
            top: -100px;
            left: -100px;

            @if ($status === 'success')
                background: #00d4aa;
            @elseif($status === 'failed')
                background: #ff4444;
            @elseif($status === 'cancelled')
                background: #ffaa00;
            @else
                background: #4488ff;
            @endif
        }

        .orb-2 {
            width: 300px;
            height: 300px;
            bottom: -80px;
            right: -80px;
            animation-delay: -4s;

            @if ($status === 'success')
                background: #0099ff;
            @elseif($status === 'failed')
                background: #ff8800;
            @elseif($status === 'cancelled')
                background: #ff6600;
            @else
                background: #aa44ff;
            @endif
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0) scale(1);
            }

            50% {
                transform: translateY(-30px) scale(1.05);
            }
        }

        /* ── Main card ── */
        .payment-card {
            background: rgba(255, 255, 255, 0.04);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            width: 100%;
            max-width: 560px;
            overflow: hidden;
            box-shadow:
                0 32px 64px rgba(0, 0, 0, 0.4),
                0 0 0 1px rgba(255, 255, 255, 0.05) inset;
        }

        /* ── Status banner ── */
        .status-banner {
            padding: 48px 40px 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .status-banner::before {
            content: '';
            position: absolute;
            inset: 0;
            opacity: 0.08;
        }

        @if ($status === 'success')
            .status-banner {
                background: linear-gradient(160deg, rgba(0, 212, 170, 0.15) 0%, rgba(0, 153, 255, 0.08) 100%);
            }

            .status-banner::before {
                background: radial-gradient(circle at 50% 0%, #00d4aa, transparent 70%);
            }
        @elseif($status === 'failed')
            .status-banner {
                background: linear-gradient(160deg, rgba(255, 68, 68, 0.15) 0%, rgba(255, 136, 0, 0.08) 100%);
            }

            .status-banner::before {
                background: radial-gradient(circle at 50% 0%, #ff4444, transparent 70%);
            }
        @elseif($status === 'cancelled')
            .status-banner {
                background: linear-gradient(160deg, rgba(255, 170, 0, 0.15) 0%, rgba(255, 102, 0, 0.08) 100%);
            }

            .status-banner::before {
                background: radial-gradient(circle at 50% 0%, #ffaa00, transparent 70%);
            }
        @else
            .status-banner {
                background: linear-gradient(160deg, rgba(68, 136, 255, 0.15) 0%, rgba(170, 68, 255, 0.08) 100%);
            }

            .status-banner::before {
                background: radial-gradient(circle at 50% 0%, #4488ff, transparent 70%);
            }
        @endif

        /* ── Icon ring ── */
        .icon-ring {
            width: 96px;
            height: 96px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            font-size: 2.6rem;
            position: relative;
        }

        .icon-ring::before {
            content: '';
            position: absolute;
            inset: -4px;
            border-radius: 50%;
            opacity: 0.3;
            animation: ring-pulse 2.5s ease-in-out infinite;
        }

        @if ($status === 'success')
            .icon-ring {
                background: linear-gradient(135deg, #00d4aa, #00b894);
                color: #fff;
                box-shadow: 0 8px 32px rgba(0, 212, 170, 0.4);
            }

            .icon-ring::before {
                background: #00d4aa;
            }

            @keyframes ring-pulse {

                0%,
                100% {
                    transform: scale(1);
                    opacity: 0.3
                }

                50% {
                    transform: scale(1.15);
                    opacity: 0.1
                }
            }
        @elseif($status === 'failed')
            .icon-ring {
                background: linear-gradient(135deg, #ff4444, #cc0000);
                color: #fff;
                box-shadow: 0 8px 32px rgba(255, 68, 68, 0.4);
            }

            .icon-ring::before {
                background: #ff4444;
            }
        @elseif($status === 'cancelled')
            .icon-ring {
                background: linear-gradient(135deg, #ffaa00, #ff8800);
                color: #fff;
                box-shadow: 0 8px 32px rgba(255, 170, 0, 0.4);
            }

            .icon-ring::before {
                background: #ffaa00;
            }
        @else
            .icon-ring {
                background: linear-gradient(135deg, #4488ff, #2255cc);
                color: #fff;
                box-shadow: 0 8px 32px rgba(68, 136, 255, 0.4);
            }

            .icon-ring::before {
                background: #4488ff;
            }
        @endif

        .status-title {
            font-size: 1.9rem;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.5px;
            margin-bottom: 8px;
        }

        .status-subtitle {
            font-size: 0.95rem;
            color: rgba(255, 255, 255, 0.6);
            font-weight: 400;
            line-height: 1.5;
        }

        /* ── Details section ── */
        .details-section {
            padding: 8px 40px 32px;
        }

        .detail-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }

        .detail-item:last-child {
            border-bottom: none;
        }

        .detail-key {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.85rem;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.45);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .detail-key i {
            font-size: 1rem;
        }

        .detail-val {
            font-size: 0.92rem;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.9);
            text-align: right;
            max-width: 55%;
            word-break: break-all;
        }

        .detail-val.mono {
            font-family: 'Courier New', monospace;
            font-size: 0.78rem;
            color: rgba(255, 255, 255, 0.6);
            background: rgba(255, 255, 255, 0.06);
            padding: 3px 8px;
            border-radius: 6px;
        }

        /* ── Status pill ── */
        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.3px;
        }

        .pill-success {
            background: rgba(0, 212, 170, 0.15);
            color: #00d4aa;
            border: 1px solid rgba(0, 212, 170, 0.3);
        }

        .pill-failed {
            background: rgba(255, 68, 68, 0.15);
            color: #ff6b6b;
            border: 1px solid rgba(255, 68, 68, 0.3);
        }

        .pill-cancelled {
            background: rgba(255, 170, 0, 0.15);
            color: #ffcc44;
            border: 1px solid rgba(255, 170, 0, 0.3);
        }

        .pill-error {
            background: rgba(68, 136, 255, 0.15);
            color: #88aaff;
            border: 1px solid rgba(68, 136, 255, 0.3);
        }

        /* ── Divider ── */
        .section-divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.08), transparent);
            margin: 0 40px;
        }

        /* ── Action buttons ── */
        .actions-section {
            padding: 28px 40px 32px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .btn-primary-action {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 15px 24px;
            border-radius: 12px;
            font-size: 0.95rem;
            font-weight: 700;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            letter-spacing: 0.2px;
        }

        @if ($status === 'success')
            .btn-primary-action {
                background: linear-gradient(135deg, #00d4aa, #00b894);
                color: #fff;
                box-shadow: 0 4px 20px rgba(0, 212, 170, 0.35);
            }

            .btn-primary-action:hover {
                background: linear-gradient(135deg, #00e6bb, #00c9a0);
                color: #fff;
                transform: translateY(-2px);
                box-shadow: 0 8px 28px rgba(0, 212, 170, 0.5);
            }
        @else
            .btn-primary-action {
                background: linear-gradient(135deg, #4488ff, #2255cc);
                color: #fff;
                box-shadow: 0 4px 20px rgba(68, 136, 255, 0.35);
            }

            .btn-primary-action:hover {
                background: linear-gradient(135deg, #5599ff, #3366dd);
                color: #fff;
                transform: translateY(-2px);
                box-shadow: 0 8px 28px rgba(68, 136, 255, 0.5);
            }
        @endif

        .btn-secondary-action {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 13px 24px;
            border-radius: 12px;
            font-size: 0.9rem;
            font-weight: 600;
            text-decoration: none;
            background: rgba(255, 255, 255, 0.06);
            color: rgba(255, 255, 255, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.2s;
        }

        .btn-secondary-action:hover {
            background: rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.9);
            border-color: rgba(255, 255, 255, 0.2);
        }

        /* ── Countdown bar ── */
        .countdown-section {
            padding: 0 40px 28px;
            text-align: center;
        }

        .countdown-text {
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.35);
            margin-bottom: 10px;
        }

        .countdown-text span {
            @if ($status === 'success')
                color: #00d4aa;
            @else
                color: #4488ff;
            @endif
            font-weight: 700;
        }

        .progress-bar-wrap {
            height: 3px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 2px;
            overflow: hidden;
        }

        .progress-bar-fill {
            height: 100%;
            border-radius: 2px;

            @if ($status === 'success')
                background: linear-gradient(90deg, #00d4aa, #0099ff);
            @else
                background: linear-gradient(90deg, #4488ff, #aa44ff);
            @endif
            animation: shrink 8s linear forwards;
        }

        @keyframes shrink {
            from {
                width: 100%;
            }

            to {
                width: 0%;
            }
        }

        /* ── Footer ── */
        .card-footer-note {
            padding: 16px 40px 28px;
            text-align: center;
            font-size: 0.78rem;
            color: rgba(255, 255, 255, 0.2);
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }

        .card-footer-note strong {
            color: rgba(255, 255, 255, 0.35);
        }

        /* ── Success particles ── */
        @if ($status === 'success')
            .particle {
                position: fixed;
                pointer-events: none;
                border-radius: 50%;
                animation: particle-rise linear forwards;
            }

            @keyframes particle-rise {
                0% {
                    transform: translateY(100vh) rotate(0deg);
                    opacity: 1;
                }

                100% {
                    transform: translateY(-20vh) rotate(720deg);
                    opacity: 0;
                }
            }
        @endif

        /* ── Responsive ── */
        @media (max-width: 576px) {
            .status-banner {
                padding: 36px 24px 28px;
            }

            .details-section {
                padding: 8px 24px 24px;
            }

            .section-divider {
                margin: 0 24px;
            }

            .actions-section {
                padding: 24px 24px 24px;
            }

            .countdown-section {
                padding: 0 24px 20px;
            }

            .card-footer-note {
                padding: 14px 24px 20px;
            }

            .status-title {
                font-size: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>

    <div class="payment-card">

        {{-- ══════════════════════════════════════
             STATUS BANNER
        ══════════════════════════════════════ --}}
        <div class="status-banner">
            <div class="icon-ring">
                @if ($status === 'success')
                    <i class="ti ti-circle-check"></i>
                @elseif($status === 'failed')
                    <i class="ti ti-circle-x"></i>
                @elseif($status === 'cancelled')
                    <i class="ti ti-ban"></i>
                @else
                    <i class="ti ti-alert-triangle"></i>
                @endif
            </div>

            @if ($status === 'success')
                <div class="status-title">Payment Successful</div>
                <div class="status-subtitle">
                    Your subscription is now <strong style="color:#00d4aa;">active</strong>.<br>
                    Welcome aboard{{ $companyName ? ', ' . $companyName : '' }}!
                </div>
            @elseif($status === 'failed')
                <div class="status-title">Payment Failed</div>
                <div class="status-subtitle">
                    Your payment could not be processed.<br>
                    <span style="color:rgba(255,255,255,0.4);">No amount has been charged.</span>
                </div>
            @elseif($status === 'cancelled')
                <div class="status-title">Payment Cancelled</div>
                <div class="status-subtitle">
                    You cancelled the payment process.<br>
                    <span style="color:rgba(255,255,255,0.4);">No amount has been charged.</span>
                </div>
            @else
                <div class="status-title">Something Went Wrong</div>
                <div class="status-subtitle">
                    {{ $message ?? 'An unexpected error occurred.' }}<br>
                    <span style="color:rgba(255,255,255,0.4);">Please contact support with your Transaction ID.</span>
                </div>
            @endif
        </div>

        {{-- ══════════════════════════════════════
             TRANSACTION DETAILS
        ══════════════════════════════════════ --}}
        <div class="details-section">

            @if ($planName)
                <div class="detail-item">
                    <div class="detail-key"><i class="ti ti-package"></i> Plan</div>
                    <div class="detail-val">{{ $planName }}</div>
                </div>
            @endif

            @if ($transaction)
                <div class="detail-item">
                    <div class="detail-key"><i class="ti ti-currency-taka"></i> Amount Paid</div>
                    <div class="detail-val">{{ $transaction->currency }} {{ number_format($transaction->amount, 2) }}
                    </div>
                </div>

                @if ($status === 'success' && $transaction->subscription)
                    <div class="detail-item">
                        <div class="detail-key"><i class="ti ti-calendar-event"></i> Valid Until</div>
                        <div class="detail-val">
                            {{ $transaction->subscription->ends_at ? $transaction->subscription->ends_at->format('d M Y') : 'Lifetime' }}
                        </div>
                    </div>
                @endif

                <div class="detail-item">
                    <div class="detail-key"><i class="ti ti-clock"></i> Date & Time</div>
                    <div class="detail-val">{{ $transaction->updated_at->format('d M Y, h:i A') }}</div>
                </div>
            @endif

            @if ($transactionId)
                <div class="detail-item">
                    <div class="detail-key"><i class="ti ti-fingerprint"></i> Transaction ID</div>
                    <div class="detail-val mono">{{ $transactionId }}</div>
                </div>
            @endif

            <div class="detail-item">
                <div class="detail-key"><i class="ti ti-shield-check"></i> Status</div>
                <div class="detail-val">
                    @if ($status === 'success')
                        <span class="status-pill pill-success"><i class="ti ti-check" style="font-size:0.75rem;"></i>
                            Active</span>
                    @elseif($status === 'failed')
                        <span class="status-pill pill-failed"><i class="ti ti-x" style="font-size:0.75rem;"></i>
                            Failed</span>
                    @elseif($status === 'cancelled')
                        <span class="status-pill pill-cancelled"><i class="ti ti-minus" style="font-size:0.75rem;"></i>
                            Cancelled</span>
                    @else
                        <span class="status-pill pill-error"><i class="ti ti-alert-circle"
                                style="font-size:0.75rem;"></i> Error</span>
                    @endif
                </div>
            </div>

        </div>

        <div class="section-divider"></div>

        {{-- ══════════════════════════════════════
             ACTION BUTTONS
        ══════════════════════════════════════ --}}
        <div class="actions-section">

            @if ($status === 'success')
                {{-- User is still authenticated — go directly to dashboard --}}
                @auth
                    <a href="{{ route('company.dashboard') }}" class="btn-primary-action" id="dashboardBtn">
                        <i class="ti ti-layout-dashboard" style="font-size:1.1rem;"></i>
                        Go to Dashboard
                    </a>
                    <a href="{{ route('company.subscription.index') }}" class="btn-secondary-action">
                        <i class="ti ti-receipt"></i>
                        View Subscription Details
                    </a>
                @else
                    {{-- Session expired — redirect to login (intended will bring them back) --}}
                    <a href="{{ route('login') }}" class="btn-primary-action" id="dashboardBtn">
                        <i class="ti ti-login" style="font-size:1.1rem;"></i>
                        Login to Dashboard
                    </a>
                @endauth
            @elseif($status === 'failed')
                @auth
                    <a href="{{ route('company.subscription.plans') }}" class="btn-primary-action">
                        <i class="ti ti-refresh" style="font-size:1.1rem;"></i>
                        Try Again — View Plans
                    </a>
                    <a href="{{ route('company.dashboard') }}" class="btn-secondary-action">
                        <i class="ti ti-home"></i>
                        Back to Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn-primary-action">
                        <i class="ti ti-login" style="font-size:1.1rem;"></i>
                        Login & Try Again
                    </a>
                @endauth
            @elseif($status === 'cancelled')
                @auth
                    <a href="{{ route('company.subscription.plans') }}" class="btn-primary-action">
                        <i class="ti ti-packages" style="font-size:1.1rem;"></i>
                        View Plans Again
                    </a>
                    <a href="{{ route('company.dashboard') }}" class="btn-secondary-action">
                        <i class="ti ti-home"></i>
                        Back to Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn-primary-action">
                        <i class="ti ti-login" style="font-size:1.1rem;"></i>
                        Back to Login
                    </a>
                @endauth
            @else
                @auth
                    <a href="{{ route('company.dashboard') }}" class="btn-primary-action">
                        <i class="ti ti-home" style="font-size:1.1rem;"></i>
                        Back to Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn-primary-action">
                        <i class="ti ti-login" style="font-size:1.1rem;"></i>
                        Back to Login
                    </a>
                @endauth
            @endif

        </div>

        {{-- ══════════════════════════════════════
             AUTO-REDIRECT COUNTDOWN (success only)
        ══════════════════════════════════════ --}}
        @if ($status === 'success')
            <div class="countdown-section" id="countdownSection">
                <div class="countdown-text">
                    Auto-redirecting in <span id="countdownNum">8</span>s
                </div>
                <div class="progress-bar-wrap">
                    <div class="progress-bar-fill" id="progressBar"></div>
                </div>
            </div>
        @endif

        {{-- ══════════════════════════════════════
             FOOTER
        ══════════════════════════════════════ --}}
        <div class="card-footer-note">
            <strong>{{ config('app.name') }}</strong> &nbsp;·&nbsp;
            @if ($transactionId)
                Ref: <strong>{{ $transactionId }}</strong> &nbsp;·&nbsp;
            @endif
            Need help? Contact support
        </div>

    </div>

    <script>
        @if ($status === 'success')
            // ── Auto-redirect countdown ──────────────────────────────────────────
            (function() {
                    var seconds = 8;
                    var numEl = document.getElementById('countdownNum');
                    var sectionEl = document.getElementById('countdownSection');
                    var dashboardBtn = document.getElementById('dashboardBtn');

                    @auth
                    var redirectUrl = '{{ route('company.dashboard') }}';
                @else
                    var redirectUrl = '{{ route('login') }}';
                @endauth

                var interval = setInterval(function() {
                    seconds--;
                    if (numEl) numEl.textContent = seconds;
                    if (seconds <= 0) {
                        clearInterval(interval);
                        window.location.href = redirectUrl;
                    }
                }, 1000);

                // Cancel countdown if user clicks the button
                if (dashboardBtn) {
                    dashboardBtn.addEventListener('click', function() {
                        clearInterval(interval);
                        if (sectionEl) sectionEl.style.display = 'none';
                    });
                }
            })();

        // ── Success particles ────────────────────────────────────────────────
        (function() {
            var colors = ['#00d4aa', '#0099ff', '#ffffff', '#00e6bb', '#66ccff'];
            for (var i = 0; i < 18; i++) {
                (function(i) {
                    setTimeout(function() {
                        var p = document.createElement('div');
                        p.className = 'particle';
                        var size = Math.random() * 8 + 4;
                        p.style.cssText = [
                            'width:' + size + 'px',
                            'height:' + size + 'px',
                            'left:' + (Math.random() * 100) + 'vw',
                            'bottom:0',
                            'background:' + colors[Math.floor(Math.random() * colors.length)],
                            'animation-duration:' + (Math.random() * 3 + 2) + 's',
                            'animation-delay:0s',
                            'opacity:' + (Math.random() * 0.6 + 0.4)
                        ].join(';');
                        document.body.appendChild(p);
                        setTimeout(function() {
                            p.remove();
                        }, 5000);
                    }, i * 150);
                })(i);
            }
        })();
        @endif
    </script>

</body>

</html>
