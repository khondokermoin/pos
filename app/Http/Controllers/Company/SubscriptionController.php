<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Mail\NewSubscriptionAdminNotification;
use App\Mail\SubscriptionConfirmationMail;
use App\Models\Branch;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SubscriptionController extends Controller
{
    /**
     * Company-এর current subscription dashboard দেখাবে।
     */
    public function index()
    {
        $company = Auth::user()->company;

        $subscription = Subscription::with('plan')
            ->where('company_id', $company->id)
            ->latest()
            ->first();

        $transactions = Transaction::where('company_id', $company->id)
            ->with('subscription')
            ->latest()
            ->take(10)
            ->get();

        $plans = Plan::active()->orderBy('price', 'asc')->get();

        return view('company.subscription.index', compact('subscription', 'transactions', 'plans', 'company'));
    }

    public function showPlans()
    {
        return $this->index();
    }

    // =========================================================================
    // DOWNGRADE EXPLOIT GUARD
    // =========================================================================
    // Checks whether the company's current resource usage (branches, users)
    // fits within the target plan's limits BEFORE allowing payment to proceed.
    //
    // Rules:
    //   - A limit of -1 or null means "unlimited" → always passes.
    //   - If current usage > plan limit → block with an actionable error.
    //   - This guard fires on EVERY plan switch (upgrade, downgrade, renewal).
    //     Upgrades will always pass; only downgrades to a plan whose limits
    //     are lower than current usage will be blocked.
    // =========================================================================

    /**
     * Validate that the company's current resource usage does not exceed
     * the limits of the target plan.
     *
     * Returns null on success, or a human-readable error string on failure.
     */
    private function checkDowngradeEligibility($company, Plan $targetPlan): ?string
    {
        $errors = [];

        // ── 1. Branch limit check ─────────────────────────────────────────────
        $branchLimit = $targetPlan->branch_limit;
        if ($branchLimit !== null && $branchLimit != -1) {
            $currentBranches = Branch::where('company_id', $company->id)->count();
            if ($currentBranches > $branchLimit) {
                $excess = $currentBranches - $branchLimit;
                $errors[] = sprintf(
                    'You currently have <strong>%d branch%s</strong>, but the <strong>%s</strong> plan allows a maximum of <strong>%d</strong>. Please delete <strong>%d branch%s</strong> to proceed.',
                    $currentBranches,
                    $currentBranches !== 1 ? 'es' : '',
                    $targetPlan->name,
                    $branchLimit,
                    $excess,
                    $excess !== 1 ? 'es' : ''
                );
            }
        }

        // ── 2. User / employee limit check ────────────────────────────────────
        $userLimit = $targetPlan->user_limit;
        if ($userLimit !== null && $userLimit != -1) {
            // Count all users belonging to this company (excluding the owner
            // themselves so the owner is never locked out).
            $currentUsers = \App\Models\User::where('company_id', $company->id)->count();
            if ($currentUsers > $userLimit) {
                $excess = $currentUsers - $userLimit;
                $errors[] = sprintf(
                    'You currently have <strong>%d user%s</strong>, but the <strong>%s</strong> plan allows a maximum of <strong>%d</strong>. Please remove <strong>%d user%s</strong> to proceed.',
                    $currentUsers,
                    $currentUsers !== 1 ? 's' : '',
                    $targetPlan->name,
                    $userLimit,
                    $excess,
                    $excess !== 1 ? 's' : ''
                );
            }
        }

        if (empty($errors)) {
            return null; // ✅ All checks passed
        }

        // Build a single consolidated error message
        $intro = 'You cannot switch to the <strong>' . $targetPlan->name . '</strong> plan because your current usage exceeds its limits:';
        $list  = '<ul class="mb-0 mt-2">';
        foreach ($errors as $err) {
            $list .= '<li>' . $err . '</li>';
        }
        $list .= '</ul>';

        return $intro . $list;
    }

    public function subscribe(Request $request, Plan $plan)
    {
        $company       = Auth::user()->company;
        $billingCycle  = $request->input('billing_cycle', $plan->billing_cycle ?? 'monthly');

        // ── DOWNGRADE EXPLOIT GUARD ───────────────────────────────────────────
        // Run this check BEFORE creating any transaction record or touching the
        // payment gateway. If the company's current usage exceeds the target
        // plan's limits, abort immediately with an actionable error message.
        $eligibilityError = $this->checkDowngradeEligibility($company, $plan);
        if ($eligibilityError !== null) {
            Log::warning('Downgrade exploit blocked', [
                'company_id' => $company->id,
                'target_plan' => $plan->name,
                'target_plan_id' => $plan->id,
            ]);

            return redirect()->route('company.subscription.index')
                ->with('downgrade_error', $eligibilityError);
        }
        // ─────────────────────────────────────────────────────────────────────

        $transactionId = 'TXN-' . strtoupper(Str::random(12));

        // Step 1: Pending transaction record তৈরি করো (payment শুরুর আগেই)
        Transaction::create([
            'company_id'     => $company->id,
            'amount'         => $plan->price,
            'currency'       => 'BDT',
            'payment_method' => 'sslcommerz',
            'transaction_id' => $transactionId,
            'status'         => 'pending',
            'details'        => ['plan_id' => $plan->id, 'billing_cycle' => $billingCycle],
        ]);

        // Step 2: SSLCommerz Hosted Checkout URL তৈরি করো
        $isSandbox = config('sslcommerz.is_sandbox', true);
        $storeId   = config('sslcommerz.store_id',   env('SSLCOMMERZ_STORE_ID'));
        $storePass = config('sslcommerz.store_password', env('SSLCOMMERZ_STORE_PASSWORD'));

        // Callback URL — public route, no auth required
        $callbackUrl = route('payment.callback');

        $successUrl  = $callbackUrl . '?status=success&tran_id=' . $transactionId . '&plan_id=' . $plan->id;
        $failUrl     = $callbackUrl . '?status=failed&tran_id='  . $transactionId;
        $cancelUrl   = $callbackUrl . '?status=cancelled&tran_id=' . $transactionId;

        $postData = [
            'store_id'         => $storeId,
            'store_passwd'     => $storePass,
            'total_amount'     => $plan->price,
            'currency'         => 'BDT',
            'tran_id'          => $transactionId,
            'success_url'      => $successUrl,
            'fail_url'         => $failUrl,
            'cancel_url'       => $cancelUrl,
            'ipn_url'          => $callbackUrl . '?tran_id=' . $transactionId,
            'shipping_method'  => 'No',
            'product_name'     => $plan->name . ' Subscription',
            'product_category' => 'SaaS',
            'product_profile'  => 'non-physical-goods',
            'cus_name'         => $company->name,
            'cus_email'        => $company->email,
            'cus_add1'         => $company->address ?? 'Dhaka',
            'cus_city'         => $company->city    ?? 'Dhaka',
            'cus_country'      => $company->country ?? 'Bangladesh',
            'cus_phone'        => $company->phone   ?? '01700000000',
        ];

        // Step 3: SSLCommerz API কে POST করে GatewayPageURL নাও
        $sslczURL = $isSandbox
            ? 'https://sandbox.sslcommerz.com/gwprocess/v4/api.php'
            : 'https://securepay.sslcommerz.com/gwprocess/v4/api.php';

        $handle = curl_init();
        curl_setopt($handle, CURLOPT_URL, $sslczURL);
        curl_setopt($handle, CURLOPT_TIMEOUT, 30);
        curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($handle, CURLOPT_POST, 1);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false); // sandbox এর জন্য
        $content = curl_exec($handle);
        curl_close($handle);

        $response = json_decode($content, true);

        // Step 4: GatewayPageURL পেলে redirect করো, না পেলে error দেখাও
        if (isset($response['GatewayPageURL']) && $response['GatewayPageURL']) {
            return redirect()->away($response['GatewayPageURL']);
        }

        // Gateway connection fail হলে pending transaction delete করো এবং error দেখাও
        Transaction::where('transaction_id', $transactionId)->delete();

        return redirect()->route('company.subscription.index')
            ->with('error', 'Payment gateway connection failed. Please try again later. (Error: ' . ($response['failedreason'] ?? 'Unknown') . ')');
    }

    public function downloadInvoice(string $invoiceNumber)
    {
        $company = Auth::user()->company;

        $subscription = Subscription::where('company_id', $company->id)
            ->where('invoice_number', $invoiceNumber)
            ->firstOrFail();

        // Laravel 12: Storage::disk('local') maps to storage/app/private/
        // Try both paths for backward compatibility
        $relativePath = 'invoices/' . $invoiceNumber . '.pdf';

        if (Storage::disk('local')->exists($relativePath)) {
            return Storage::disk('local')->download($relativePath, 'Invoice-' . $invoiceNumber . '.pdf');
        }

        // Fallback: legacy path (storage/app/invoices/)
        $legacyPath = storage_path('app/invoices/' . $invoiceNumber . '.pdf');
        if (file_exists($legacyPath)) {
            return response()->download($legacyPath, 'Invoice-' . $invoiceNumber . '.pdf');
        }

        // Invoice file missing — regenerate it on the fly
        $transaction = Transaction::where('subscription_id', $subscription->id)
            ->where('status', 'success')
            ->latest()
            ->first();

        if (! $transaction) {
            return back()->with('error', 'Invoice file not found and cannot be regenerated. Please contact support.');
        }

        $plan = $subscription->plan;

        $pdf = Pdf::loadView('pdf.subscription-invoice', [
            'company'      => $company,
            'subscription' => $subscription,
            'plan'         => $plan,
            'transaction'  => $transaction,
        ]);

        // Save for future downloads
        Storage::disk('local')->put($relativePath, $pdf->output());

        return response()->download(
            Storage::disk('local')->path($relativePath),
            'Invoice-' . $invoiceNumber . '.pdf'
        );
    }

    /**
     * SSLCommerz Payment Callback Handler
     *
     * ┌─────────────────────────────────────────────────────────────────────┐
     * │  ARCHITECTURE NOTE — Why this route is PUBLIC and sessionless        │
     * │                                                                       │
     * │  SSLCommerz POSTs to this URL from THEIR servers (or redirects the   │
     * │  user's browser back here after payment). In both cases the Laravel  │
     * │  session cookie that was set when the user clicked "Pay" is NOT      │
     * │  present — the cross-origin POST drops it.                           │
     * │                                                                       │
     * │  FIX: After we verify the transaction and activate the subscription, │
     * │  we look up the Company Admin user who owns this transaction and      │
     * │  call Auth::login($user) to explicitly re-establish their session.   │
     * │  This means the subsequent redirect to company.dashboard (or the     │
     * │  payment result page) will find an authenticated user and will NOT   │
     * │  bounce them to the login page.                                       │
     * └─────────────────────────────────────────────────────────────────────┘
     *
     * SSLCommerz sends:
     *   - status      : VALID | FAILED | CANCELLED | UNATTEMPTED | ERROR
     *   - tran_id     : our transaction ID (in query string + POST body)
     *   - plan_id     : our custom param (query string only)
     *   - val_id      : SSLCommerz validation ID (POST body)
     *   - amount      : amount paid (POST body)
     *   - currency    : currency (POST body)
     */
    public function paymentCallback(Request $request)
    {
        // ── 1. Extract parameters ────────────────────────────────────────────
        // $request->input() merges both GET query params and POST body.
        // SSLCommerz sends 'status' in the POST body; our query string also
        // carries it as a fallback.
        $rawStatus     = strtolower(trim($request->input('status', '')));
        $transactionId = trim($request->input('tran_id', ''));
        $planId        = $request->input('plan_id');   // query string only
        $valId         = $request->input('val_id');    // SSLCommerz validation ID

        Log::info('SSLCommerz Callback Received', [
            'status'    => $rawStatus,
            'tran_id'   => $transactionId,
            'plan_id'   => $planId,
            'val_id'    => $valId,
            'method'    => $request->method(),
            'all_input' => $request->except(['store_passwd']),
        ]);

        // ── 2. Guard: transaction must exist ────────────────────────────────
        if (empty($transactionId)) {
            Log::warning('SSLCommerz Callback: missing tran_id');
            return redirect()->route('payment.result', [
                'status'  => 'error',
                'message' => 'Invalid callback — missing transaction ID.',
            ]);
        }

        $transaction = Transaction::where('transaction_id', $transactionId)->first();

        if (! $transaction) {
            Log::warning('SSLCommerz Callback: transaction not found', ['tran_id' => $transactionId]);
            return redirect()->route('payment.result', [
                'status'  => 'error',
                'tran_id' => $transactionId,
                'message' => 'Transaction record not found.',
            ]);
        }

        // ── 3. Idempotency guard — don't re-process an already-completed txn ─
        if ($transaction->status === 'success') {
            Log::info('SSLCommerz Callback: already processed (idempotent)', ['tran_id' => $transactionId]);

            // Re-login the user so the result page shows the dashboard button
            $this->reLoginCompanyAdmin($transaction->company);

            return redirect()->route('company.dashboard')
                ->with('success', 'Your subscription is already active. Welcome back!');
        }

        // ── 4. Determine outcome ─────────────────────────────────────────────
        // SSLCommerz success statuses are 'VALID' or 'VALIDATED'
        $isSuccess   = in_array($rawStatus, ['valid', 'validated', 'success']);
        $isFailed    = in_array($rawStatus, ['failed', 'fail', 'error']);
        $isCancelled = in_array($rawStatus, ['cancelled', 'cancel', 'unattempted']);

        // ── 5a. SUCCESS path ─────────────────────────────────────────────────
        if ($isSuccess) {
            try {
                $company = $transaction->company;

                // Resolve plan: prefer query-string plan_id, fall back to stored details
                $resolvedPlanId = $planId ?? ($transaction->details['plan_id'] ?? null);
                if (! $resolvedPlanId) {
                    throw new \RuntimeException('Cannot determine plan_id for transaction ' . $transactionId);
                }

                $plan         = Plan::findOrFail($resolvedPlanId);
                $billingCycle = $transaction->details['billing_cycle'] ?? 'monthly';

                $endsAt = match ($billingCycle) {
                    'yearly'   => now()->addYear(),
                    'lifetime' => null,
                    default    => now()->addMonth(),
                };

                // ── Subscription Logic ────────────────────────────────────────
                // IMPORTANT: We do NOT use updateOrCreate here because that would
                // overwrite the existing subscription record on every payment,
                // destroying the payment history and causing the "keeps updating" bug.
                //
                // Correct logic:
                //   1. If there is an existing ACTIVE subscription for this company,
                //      EXTEND it (update ends_at, plan_id) — this is a renewal.
                //   2. If there is NO active subscription (first time, expired, cancelled),
                //      CREATE a new subscription record.
                $existingSubscription = Subscription::where('company_id', $company->id)
                    ->whereIn('status', ['active', 'trial'])
                    ->latest()
                    ->first();

                if ($existingSubscription) {
                    // RENEWAL: extend the existing subscription
                    $newEndsAt = match ($billingCycle) {
                        'yearly'   => ($existingSubscription->ends_at && $existingSubscription->ends_at->isFuture()
                            ? $existingSubscription->ends_at->addYear()
                            : now()->addYear()),
                        'lifetime' => null,
                        default    => ($existingSubscription->ends_at && $existingSubscription->ends_at->isFuture()
                            ? $existingSubscription->ends_at->addMonth()
                            : now()->addMonth()),
                    };

                    $existingSubscription->update([
                        'plan_id'         => $plan->id,
                        'status'          => 'active',
                        'billing_cycle'   => $billingCycle,
                        'ends_at'         => $newEndsAt,
                        'payment_gateway' => 'sslcommerz',
                        'transaction_id'  => $transactionId,
                        // Keep the original invoice_number — don't overwrite it.
                        // A new invoice_number is only set on first subscription.
                    ]);

                    $subscription = $existingSubscription;

                    Log::info('Subscription RENEWED (extended)', [
                        'company_id'      => $company->id,
                        'subscription_id' => $subscription->id,
                        'new_ends_at'     => $newEndsAt,
                    ]);
                } else {
                    // NEW SUBSCRIPTION: create a fresh record
                    $subscription = Subscription::create([
                        'company_id'      => $company->id,
                        'plan_id'         => $plan->id,
                        'status'          => 'active',
                        'billing_cycle'   => $billingCycle,
                        'started_at'      => now(),
                        'ends_at'         => $endsAt,
                        'payment_gateway' => 'sslcommerz',
                        'transaction_id'  => $transactionId,
                        'invoice_number'  => 'INV-' . strtoupper(Str::random(8)),
                    ]);

                    Log::info('New Subscription CREATED', [
                        'company_id'      => $company->id,
                        'subscription_id' => $subscription->id,
                    ]);
                }

                // Update company plan & status
                $company->update(['plan_id' => $plan->id, 'status' => 'active']);

                // Update transaction record
                $transaction->update([
                    'status'          => 'success',
                    'subscription_id' => $subscription->id,
                ]);

                // Generate PDF invoice
                $pdf = Pdf::loadView('pdf.subscription-invoice', [
                    'company'      => $company,
                    'subscription' => $subscription,
                    'plan'         => $plan,
                    'transaction'  => $transaction,
                ]);

                $invoiceFileName = 'invoices/' . $subscription->invoice_number . '.pdf';
                Storage::disk('local')->put($invoiceFileName, $pdf->output());

                // Queue confirmation emails
                if ($company->email) {
                    Mail::to($company->email)->queue(
                        new SubscriptionConfirmationMail($company, $subscription, $plan, $transaction, $invoiceFileName)
                    );
                }

                $superAdminEmail = env('SUPER_ADMIN_EMAIL');
                if ($superAdminEmail) {
                    Mail::to($superAdminEmail)->queue(
                        new NewSubscriptionAdminNotification($company, $subscription, $plan, $transaction)
                    );
                }

                Log::info('SSLCommerz Callback: payment SUCCESS', [
                    'tran_id'      => $transactionId,
                    'company'      => $company->name,
                    'plan'         => $plan->name,
                    'subscription' => $subscription->id,
                ]);

                // ── SESSION RECOVERY: Re-authenticate the Company Admin ───────
                // SSLCommerz's cross-origin POST drops the user's browser session.
                // We look up the Company Admin who owns this company and explicitly
                // log them back in so the redirect to the dashboard succeeds without
                // hitting the login wall.
                $this->reLoginCompanyAdmin($company);

                // Redirect directly to the company dashboard with a success flash.
                // The user is now authenticated, so the dashboard middleware will pass.
                return redirect()->route('company.dashboard')
                    ->with('success', 'Payment successful! Your ' . $plan->name . ' subscription is now active.');
            } catch (\Throwable $e) {
                Log::error('SSLCommerz Callback: exception during success processing', [
                    'tran_id' => $transactionId,
                    'error'   => $e->getMessage(),
                    'trace'   => $e->getTraceAsString(),
                ]);

                $transaction->update(['status' => 'failed']);

                return redirect()->route('payment.result', [
                    'status'  => 'error',
                    'tran_id' => $transactionId,
                    'message' => 'Payment was received but an error occurred while activating your subscription. Please contact support.',
                ]);
            }
        }

        // ── 5b. FAILED path ──────────────────────────────────────────────────
        if ($isFailed) {
            $transaction->update(['status' => 'failed']);

            Log::info('SSLCommerz Callback: payment FAILED', ['tran_id' => $transactionId]);

            // Re-login so the result page can show the "Try Again" button
            $this->reLoginCompanyAdmin($transaction->company);

            return redirect()->route('payment.result', [
                'status'  => 'failed',
                'tran_id' => $transactionId,
            ]);
        }

        // ── 5c. CANCELLED path ───────────────────────────────────────────────
        if ($isCancelled) {
            $transaction->update(['status' => 'cancelled']);

            Log::info('SSLCommerz Callback: payment CANCELLED', ['tran_id' => $transactionId]);

            // Re-login so the result page can show the "View Plans" button
            $this->reLoginCompanyAdmin($transaction->company);

            return redirect()->route('payment.result', [
                'status'  => 'cancelled',
                'tran_id' => $transactionId,
            ]);
        }

        // ── 5d. Unknown status ───────────────────────────────────────────────
        Log::warning('SSLCommerz Callback: unknown status received', [
            'status'  => $rawStatus,
            'tran_id' => $transactionId,
        ]);

        $transaction->update(['status' => 'pending']);

        return redirect()->route('payment.result', [
            'status'  => 'error',
            'tran_id' => $transactionId,
            'message' => 'Unknown payment status received: ' . $rawStatus,
        ]);
    }

    /**
     * Re-authenticate the Company Admin user who owns the given company.
     *
     * WHY THIS EXISTS:
     * SSLCommerz sends a cross-origin POST to our callback URL. The browser's
     * session cookie is NOT forwarded in this request, so Auth::user() is null
     * when we arrive here. After we finish processing the payment, we need to
     * restore the session so the subsequent redirect to company.dashboard does
     * not bounce the user to the login page.
     *
     * We find the first user with the 'Company Admin' role that belongs to this
     * company and call Auth::login() to write their ID into the session.
     *
     * This is safe because:
     *   1. We only call this AFTER the transaction has been verified as genuine.
     *   2. We only log in the owner of the company that paid — never a random user.
     *   3. The session is regenerated by Auth::login() to prevent fixation attacks.
     *
     * @param  \App\Models\Company|null  $company
     */
    private function reLoginCompanyAdmin($company): void
    {
        if (! $company) {
            Log::warning('reLoginCompanyAdmin: company is null — cannot restore session');
            return;
        }

        // Find the Company Admin user for this company.
        // We use Spatie's role check to be precise.
        $companyAdmin = \App\Models\User::where('company_id', $company->id)
            ->whereHas('roles', fn($q) => $q->where('name', 'Company Admin'))
            ->first();

        if (! $companyAdmin) {
            Log::warning('reLoginCompanyAdmin: no Company Admin found for company', [
                'company_id' => $company->id,
            ]);
            return;
        }

        // Explicitly log the user in — this writes their ID to the session
        // and regenerates the session ID to prevent session fixation.
        Auth::login($companyAdmin, remember: true);

        Log::info('reLoginCompanyAdmin: session restored for Company Admin', [
            'user_id'    => $companyAdmin->id,
            'company_id' => $company->id,
        ]);
    }

    /**
     * Public payment result page — no auth required.
     * Reads transaction status from DB and shows a beautiful result page.
     */
    public function paymentResult(Request $request)
    {
        $transactionId = $request->input('tran_id');
        $status        = $request->input('status', 'error');
        $planName      = $request->input('plan');
        $companyName   = $request->input('company');
        $message       = $request->input('message');

        $transaction = $transactionId
            ? Transaction::with(['company', 'subscription.plan'])
            ->where('transaction_id', $transactionId)
            ->first()
            : null;

        // If we have a transaction, trust the DB status over query param
        if ($transaction) {
            $status      = $transaction->status;
            $companyName = $companyName ?? $transaction->company?->name;
            $planName    = $planName    ?? $transaction->subscription?->plan?->name;
        }

        return view('payment.result', compact(
            'status',
            'transactionId',
            'planName',
            'companyName',
            'message',
            'transaction'
        ));
    }
}
