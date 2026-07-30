<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SaleController extends Controller
{
    public function index()
    {
        $branchId = Auth::user()->branch_id;

        $sales = Sale::with(['customer'])
            ->withCount('items')
            ->where('branch_id', $branchId)
            ->latest()
            ->paginate(20);

        return view('branch.sales.index', compact('sales'));
    }

    public function show(Sale $sale)
    {
        // Ensure the sale belongs to the authenticated user's branch
        abort_unless($sale->branch_id === Auth::user()->branch_id, 403);

        $sale->load(['customer', 'items.variant.product']);

        return view('branch.sales.show', compact('sale'));
    }
}
