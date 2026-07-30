<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Subscription;
use App\Models\Transaction;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->input('type', 'revenue');
        $from = $request->input('from', now()->startOfMonth()->format('Y-m-d'));
        $to   = $request->input('to', now()->format('Y-m-d'));

        // ── Tenant Usage Report ───────────────────────────────────────────
        if ($type === 'tenant-usage') {
            $companies = Company::with('plan')
                ->withCount(['users', 'branches'])
                ->latest()
                ->paginate(20);

            // Per-company product & sale counts (without global scope interference)
            $productCounts = Product::selectRaw('company_id, COUNT(*) as total')
                ->groupBy('company_id')
                ->pluck('total', 'company_id');

            $saleCounts = Sale::selectRaw('company_id, COUNT(*) as total')
                ->groupBy('company_id')
                ->pluck('total', 'company_id');

            return view('super-admin.reports.tenant-usage', compact(
                'type',
                'companies',
                'productCounts',
                'saleCounts',
                'from',
                'to'
            ));
        }

        // ── Revenue Report (default) ──────────────────────────────────────
        $revenue = Transaction::where('status', 'success')
            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->sum('amount');

        $newSubscriptions = Subscription::whereBetween('started_at', [$from, $to])->count();
        $cancelledSubs    = Subscription::where('status', 'cancelled')
            ->whereBetween('cancelled_at', [$from, $to])
            ->count();
        $newCompanies = Company::whereBetween('created_at', [$from, $to])->count();

        $transactions = Transaction::with(['company', 'subscription.plan'])
            ->where('status', 'success')
            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->latest()
            ->paginate(20);

        return view('super-admin.reports.index', compact(
            'type',
            'revenue',
            'newSubscriptions',
            'cancelledSubs',
            'newCompanies',
            'transactions',
            'from',
            'to'
        ));
    }
}
