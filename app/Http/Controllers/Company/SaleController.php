<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;

        $query = Sale::with(['customer', 'branch'])
            ->withCount('items')
            ->where('company_id', $companyId);

        if ($request->filled('search')) {
            $query->where('invoice_no', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $sales = $query->latest()->paginate(20);
        $totalRevenue = Sale::where('company_id', $companyId)->sum('total_amount');
        $todayRevenue = Sale::where('company_id', $companyId)->whereDate('created_at', today())->sum('total_amount');

        return view('company.sales.index', compact('sales', 'totalRevenue', 'todayRevenue'));
    }
}
