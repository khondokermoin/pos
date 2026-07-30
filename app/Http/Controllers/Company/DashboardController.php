<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Stock;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $companyId = Auth::user()->company_id;

        $todaySales     = Sale::where('company_id', $companyId)->whereDate('created_at', today())->count();
        $todayRevenue   = Sale::where('company_id', $companyId)->whereDate('created_at', today())->sum('total_amount');
        $monthRevenue   = Sale::where('company_id', $companyId)->whereMonth('created_at', now()->month)->sum('total_amount');
        $totalProducts  = Product::where('company_id', $companyId)->count();
        $totalCustomers = Customer::where('company_id', $companyId)->count();
        $lowStockCount  = Stock::where('company_id', $companyId)->where('quantity', '<=', 5)->count();

        $recentSales = Sale::with('customer')
            ->where('company_id', $companyId)
            ->latest()
            ->take(8)
            ->get();

        return view('company.dashboard', compact(
            'todaySales',
            'todayRevenue',
            'totalProducts',
            'totalCustomers',
            'lowStockCount',
            'monthRevenue',
            'recentSales'
        ));
    }
}
