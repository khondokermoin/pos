<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Stock;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user     = Auth::user();
        $branchId = $user->branch_id;

        $todaySales    = Sale::where('branch_id', $branchId)->whereDate('created_at', today())->count();
        $todayRevenue  = Sale::where('branch_id', $branchId)->whereDate('created_at', today())->sum('total_amount');
        $lowStockCount = Stock::where('branch_id', $branchId)->where('quantity', '<=', 5)->count();
        $totalProducts = Stock::where('branch_id', $branchId)->distinct('variant_id')->count('variant_id');

        $recentSales = Sale::with('customer')
            ->withCount('items')
            ->where('branch_id', $branchId)
            ->latest()
            ->take(10)
            ->get();

        return view('branch.dashboard', compact(
            'todaySales',
            'todayRevenue',
            'lowStockCount',
            'totalProducts',
            'recentSales'
        ));
    }
}
