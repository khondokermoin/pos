<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    private function branchId(): int
    {
        return Auth::user()->branch_id;
    }

    private function companyId(): int
    {
        return Auth::user()->company_id;
    }

    /**
     * Daily Sales Report (existing route: branch.reports.daily-sales)
     */
    public function dailySales(Request $request)
    {
        $branchId = $this->branchId();
        $from     = $request->input('from', now()->startOfMonth()->format('Y-m-d'));
        $to       = $request->input('to', now()->format('Y-m-d'));

        $sales = Sale::with(['customer', 'user'])
            ->where('branch_id', $branchId)
            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->latest()
            ->paginate(20);

        $totalSales   = $sales->total();
        $totalRevenue = Sale::where('branch_id', $branchId)
            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->sum('total_amount');
        $totalDiscount = Sale::where('branch_id', $branchId)
            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->sum('discount');

        return view('branch.reports.daily-sales', compact(
            'sales',
            'totalSales',
            'totalRevenue',
            'totalDiscount',
            'from',
            'to'
        ));
    }

    /**
     * Today's Summary Report (new route: branch.reports.today)
     */
    public function today(Request $request)
    {
        $branchId  = $this->branchId();
        $today     = now()->format('Y-m-d');

        // Today's sales
        $todaySales = Sale::where('branch_id', $branchId)
            ->whereDate('created_at', $today)
            ->get();

        $totalRevenue  = $todaySales->sum('total_amount');
        $totalDiscount = $todaySales->sum('discount');
        $totalOrders   = $todaySales->count();
        $cashSales     = $todaySales->where('payment_method', 'cash')->sum('total_amount');
        $cardSales     = $todaySales->where('payment_method', 'card')->sum('total_amount');
        $mobileSales   = $todaySales->where('payment_method', 'mobile_banking')->sum('total_amount');

        // Today's expenses
        $todayExpenses = Expense::where('branch_id', $branchId)
            ->whereDate('expense_date', $today)
            ->sum('amount');

        // Top selling items today
        $topItems = SaleItem::whereHas('sale', fn($q) => $q->where('branch_id', $branchId)->whereDate('created_at', $today))
            ->select('variant_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as total_amount'))
            ->with('variant.product')
            ->groupBy('variant_id')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();

        // Hourly sales breakdown
        $hourlySales = Sale::where('branch_id', $branchId)
            ->whereDate('created_at', $today)
            ->select(DB::raw('HOUR(created_at) as hour'), DB::raw('COUNT(*) as count'), DB::raw('SUM(total_amount) as revenue'))
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        return view('branch.reports.today', compact(
            'totalRevenue',
            'totalDiscount',
            'totalOrders',
            'cashSales',
            'cardSales',
            'mobileSales',
            'todayExpenses',
            'topItems',
            'hourlySales',
            'today'
        ));
    }

    /**
     * Inventory Report (new route: branch.reports.inventory)
     */
    public function inventory(Request $request)
    {
        $branchId  = $this->branchId();
        $companyId = $this->companyId();

        $query = Stock::with(['variant.product.category'])
            ->where('branch_id', $branchId)
            ->whereHas('variant.product', fn($q) => $q->where('company_id', $companyId));

        if ($request->filled('status')) {
            match ($request->status) {
                'out_of_stock' => $query->where('quantity', 0),
                'low_stock'    => $query->where('quantity', '>', 0)->where('quantity', '<=', 5),
                'in_stock'     => $query->where('quantity', '>', 5),
                default        => null,
            };
        }

        if ($request->filled('search')) {
            $query->whereHas('variant.product', fn($q) => $q->where('name', 'like', '%' . $request->search . '%'));
        }

        $stocks = $query->orderBy('quantity', 'asc')->paginate(25);

        $totalItems    = Stock::where('branch_id', $branchId)->count();
        $outOfStock    = Stock::where('branch_id', $branchId)->where('quantity', 0)->count();
        $lowStock      = Stock::where('branch_id', $branchId)->where('quantity', '>', 0)->where('quantity', '<=', 5)->count();
        $totalStockVal = Stock::with('variant')
            ->where('branch_id', $branchId)
            ->get()
            ->sum(fn($s) => $s->quantity * ($s->variant->cost_price ?? 0));

        return view('branch.reports.inventory', compact(
            'stocks',
            'totalItems',
            'outOfStock',
            'lowStock',
            'totalStockVal'
        ));
    }
}
