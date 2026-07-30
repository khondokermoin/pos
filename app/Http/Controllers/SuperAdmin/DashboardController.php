<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ── Billing Stats ─────────────────────────────────────────────────
        $billingStats = [
            'monthly_revenue'      => Transaction::where('status', 'success')
                ->whereMonth('created_at', now()->month)
                ->sum('amount'),
            'total_revenue'        => Transaction::where('status', 'success')->sum('amount'),
            'active_subscriptions' => Subscription::where('status', 'active')->count(),
            'expiring_soon'        => Subscription::where('status', 'active')
                ->whereBetween('ends_at', [now(), now()->addDays(7)])
                ->count(),
            'total_plans'          => Plan::active()->count(),
        ];

        // ── Company Status Stats ──────────────────────────────────────────
        $companyStats = [
            'total'     => Company::count(),
            'active'    => Company::where('status', 'active')->count(),
            'trial'     => Company::where('status', 'trial')->count(),
            'suspended' => Company::where('status', 'suspended')->count(),
        ];

        // ── Revenue Chart (last 6 months) ─────────────────────────────────
        $revenueChart = Transaction::where('status', 'success')
            ->where('created_at', '>=', now()->subMonths(6))
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(amount) as total')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->map(fn($row) => [
                'label' => date('M Y', mktime(0, 0, 0, $row->month, 1, $row->year)),
                'total' => (float) $row->total,
            ]);

        // ── Plan Distribution ─────────────────────────────────────────────
        $planDistribution = Plan::withCount(['subscriptions' => fn($q) => $q->where('status', 'active')])
            ->having('subscriptions_count', '>', 0)
            ->get(['id', 'name', 'subscriptions_count']);

        // ── Expiring Soon (next 7 days) ───────────────────────────────────
        $expiringSoon = Subscription::with(['company', 'plan'])
            ->where('status', 'active')
            ->whereBetween('ends_at', [now(), now()->addDays(7)])
            ->orderBy('ends_at')
            ->get();

        // ── Recent Company Signups (last 5) ───────────────────────────────
        $recentCompanies = Company::with('plan')
            ->latest()
            ->limit(5)
            ->get();

        // ── Recent Successful Transactions (last 5) ───────────────────────
        $recentTransactions = Transaction::with(['company', 'subscription.plan'])
            ->where('status', 'success')
            ->latest()
            ->limit(5)
            ->get();

        return view('super-admin.dashboard', compact(
            'billingStats',
            'companyStats',
            'revenueChart',
            'planDistribution',
            'expiringSoon',
            'recentCompanies',
            'recentTransactions'
        ));
    }
}
