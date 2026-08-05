<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * OnlineOrderReportController — Company Admin reporting for online orders.
 *
 * ARCHITECTURE: Blade (backend remains 100% Blade — Phase 2 cancelled)
 *
 * REPORTS PROVIDED:
 *   1. Summary stats  — total online orders, revenue, unique locations, branches used
 *   2. Orders by city — which cities/areas orders came from (with counts & revenue)
 *   3. Orders by branch — which branches received and fulfilled online orders
 *   4. Recent orders table — paginated list of all online orders with routing info
 *
 * ACCESS: Company Admin only (scoped to auth user's company_id)
 */
class OnlineOrderReportController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;

        // ── Date Range Filter ──────────────────────────────────────────────────
        $dateFrom = $request->input('date_from', now()->startOfMonth()->toDateString());
        $dateTo   = $request->input('date_to', now()->toDateString());

        // ── Summary Stats ──────────────────────────────────────────────────────
        $baseQuery = Sale::where('company_id', $companyId)
            ->where('order_type', 'online')
            ->whereBetween(DB::raw('DATE(created_at)'), [$dateFrom, $dateTo]);

        $totalOrders   = (clone $baseQuery)->count();
        $totalRevenue  = (clone $baseQuery)->sum('total_amount');
        $pendingOrders = (clone $baseQuery)->where('status', 'pending')->count();
        $uniqueCities  = (clone $baseQuery)
            ->whereNotNull('delivery_city')
            ->distinct('delivery_city')
            ->count('delivery_city');

        // ── Orders by City/District ────────────────────────────────────────────
        $ordersByCity = (clone $baseQuery)
            ->select(
                'delivery_city',
                'delivery_district',
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(total_amount) as total_revenue'),
                DB::raw('COUNT(DISTINCT routed_branch_id) as branches_used')
            )
            ->whereNotNull('delivery_city')
            ->groupBy('delivery_city', 'delivery_district')
            ->orderByDesc('order_count')
            ->get();

        // ── Orders by Branch ───────────────────────────────────────────────────
        // NOTE: We use a raw aggregate query (not a full Eloquent model query), so
        // Eloquent relationships are not available on the result rows. Instead we
        // eager-load all needed branches in a single query after the aggregate runs
        // to avoid N+1 queries.
        $ordersByBranchRaw = (clone $baseQuery)
            ->select(
                'routed_branch_id',
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(total_amount) as total_revenue'),
                DB::raw('COUNT(DISTINCT delivery_city) as cities_served'),
                DB::raw('AVG(routing_distance_km) as avg_distance_km'),
                DB::raw('SUM(CASE WHEN routing_method = "coverage_match" THEN 1 ELSE 0 END) as coverage_matches'),
                DB::raw('SUM(CASE WHEN routing_method = "distance" THEN 1 ELSE 0 END) as distance_matches'),
                DB::raw('SUM(CASE WHEN routing_method = "default" THEN 1 ELSE 0 END) as default_matches'),
                DB::raw('SUM(CASE WHEN routing_method = "fallback" THEN 1 ELSE 0 END) as fallback_matches')
            )
            ->whereNotNull('routed_branch_id')
            ->groupBy('routed_branch_id')
            ->orderByDesc('order_count')
            ->get();

        // Eager-load all referenced branches in ONE query (avoids N+1)
        $branchIds      = $ordersByBranchRaw->pluck('routed_branch_id')->filter()->unique()->values();
        $branchesKeyed  = Branch::whereIn('id', $branchIds)->get()->keyBy('id');

        $ordersByBranch = $ordersByBranchRaw->map(function ($row) use ($branchesKeyed) {
            $row->branch = $branchesKeyed->get($row->routed_branch_id);
            return $row;
        });

        // ── Routing Method Breakdown ───────────────────────────────────────────
        $routingBreakdown = (clone $baseQuery)
            ->select('routing_method', DB::raw('COUNT(*) as count'))
            ->whereNotNull('routing_method')
            ->groupBy('routing_method')
            ->pluck('count', 'routing_method')
            ->toArray();

        // ── Recent Online Orders (paginated) ───────────────────────────────────
        $recentOrders = Sale::where('company_id', $companyId)
            ->where('order_type', 'online')
            ->whereBetween(DB::raw('DATE(created_at)'), [$dateFrom, $dateTo])
            ->with(['routedBranch', 'customer'])
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        // ── All Branches (for filter dropdown) ────────────────────────────────
        $branches = Branch::where('company_id', $companyId)
            ->where('status', 'active')
            ->get(['id', 'name', 'city']);

        return view('company.reports.online_orders', compact(
            'totalOrders',
            'totalRevenue',
            'pendingOrders',
            'uniqueCities',
            'ordersByCity',
            'ordersByBranch',
            'routingBreakdown',
            'recentOrders',
            'branches',
            'dateFrom',
            'dateTo'
        ));
    }
}
