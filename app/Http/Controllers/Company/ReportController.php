<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Stock;
use App\Models\Supplier;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function sales(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $from = $request->input('from', now()->startOfMonth()->format('Y-m-d'));
        $to = $request->input('to', now()->format('Y-m-d'));

        $totalSales = Sale::where('company_id', $companyId)
            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->count();
        $totalRevenue = Sale::where('company_id', $companyId)
            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->sum('total_amount');
        $totalDiscount = Sale::where('company_id', $companyId)
            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->sum('discount');

        $sales = Sale::with(['customer', 'branch'])
            ->where('company_id', $companyId)
            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->latest()
            ->paginate(20);

        return view('company.reports.daily-sales', compact('sales', 'totalSales', 'totalRevenue', 'totalDiscount', 'from', 'to'));
    }

    public function stock()
    {
        $companyId = Auth::user()->company_id;

        $stocks = Stock::with(['variant.product', 'branch'])
            ->whereHas('variant.product', fn($q) => $q->where('company_id', $companyId))
            ->orderBy('quantity', 'asc')
            ->paginate(20);

        $lowStockCount = Stock::whereHas('variant.product', fn($q) => $q->where('company_id', $companyId))
            ->where('quantity', '<=', 5)
            ->count();
        $outOfStockCount = Stock::whereHas('variant.product', fn($q) => $q->where('company_id', $companyId))
            ->where('quantity', 0)
            ->count();

        return view('company.reports.stock', compact('stocks', 'lowStockCount', 'outOfStockCount'));
    }

    /**
     * Balance Sheet — assets vs liabilities snapshot.
     */
    public function balanceSheet(Request $request)
    {
        $companyId = Auth::user()->company_id;

        // Placeholder totals — replace with real accounting data once cash_accounts / assets / loans tables exist
        $totalAssets      = 0;
        $totalLiabilities = 0;
        $equity           = $totalAssets - $totalLiabilities;

        return view('company.reports.balance-sheet', compact('totalAssets', 'totalLiabilities', 'equity'));
    }

    /**
     * Profit & Loss — revenue minus cost of goods sold and expenses.
     */
    public function profitLoss(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $from = $request->input('from', now()->startOfMonth()->format('Y-m-d'));
        $to   = $request->input('to', now()->format('Y-m-d'));

        $totalRevenue = Sale::where('company_id', $companyId)
            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->sum('total_amount');

        $totalPurchases = Purchase::where('company_id', $companyId)
            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->sum('total_amount');

        $totalExpenses = Expense::where('company_id', $companyId)
            ->whereBetween('expense_date', [$from, $to])
            ->sum('amount');

        $grossProfit = $totalRevenue - $totalPurchases;
        $netProfit   = $grossProfit - $totalExpenses;

        return view('company.reports.profit-loss', compact(
            'totalRevenue', 'totalPurchases', 'totalExpenses',
            'grossProfit', 'netProfit', 'from', 'to'
        ));
    }

    /**
     * Expense Report — detailed breakdown of all expenses.
     */
    public function expenses(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $from = $request->input('from', now()->startOfMonth()->format('Y-m-d'));
        $to   = $request->input('to', now()->format('Y-m-d'));

        $expenses = Expense::with(['branch', 'createdBy'])
            ->where('company_id', $companyId)
            ->whereBetween('expense_date', [$from, $to])
            ->latest()
            ->paginate(20);

        $totalExpenses = Expense::where('company_id', $companyId)
            ->whereBetween('expense_date', [$from, $to])
            ->sum('amount');

        // Group by category for chart data
        $byCategory = Expense::where('company_id', $companyId)
            ->whereBetween('expense_date', [$from, $to])
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        return view('company.reports.expenses', compact('expenses', 'totalExpenses', 'byCategory', 'from', 'to'));
    }

    /**
     * Supplier Payable — outstanding amounts owed to suppliers.
     */
    public function supplierPayable(Request $request)
    {
        $companyId = Auth::user()->company_id;

        $suppliers = Supplier::where('company_id', $companyId)
            ->withSum(['purchases as total_purchased' => fn($q) => $q->where('company_id', $companyId)], 'total_amount')
            ->withSum(['purchases as total_paid' => fn($q) => $q->where('company_id', $companyId)], 'paid_amount')
            ->get()
            ->map(function ($supplier) {
                $supplier->balance_due = ($supplier->total_purchased ?? 0) - ($supplier->total_paid ?? 0);
                return $supplier;
            });

        $grandTotal = $suppliers->sum('balance_due');

        return view('company.reports.supplier-payable', compact('suppliers', 'grandTotal'));
    }

    /**
     * Client Receivable — outstanding amounts owed by customers.
     */
    public function clientReceivable(Request $request)
    {
        $companyId = Auth::user()->company_id;

        $customers = Customer::where('company_id', $companyId)
            ->withSum(['sales as total_billed' => fn($q) => $q->where('company_id', $companyId)], 'total_amount')
            ->withSum(['sales as total_received' => fn($q) => $q->where('company_id', $companyId)], 'paid_amount')
            ->get()
            ->map(function ($customer) {
                $customer->balance_due = ($customer->total_billed ?? 0) - ($customer->total_received ?? 0);
                return $customer;
            });

        $grandTotal = $customers->sum('balance_due');

        return view('company.reports.client-receivable', compact('customers', 'grandTotal'));
    }
}
