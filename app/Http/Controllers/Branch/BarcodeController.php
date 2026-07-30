<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use App\Models\BarcodeSetting;
use App\Models\ProductVariant;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BarcodeController extends Controller
{
    private function branchId(): int
    {
        return Auth::user()->branch_id;
    }

    private function companyId(): int
    {
        return Auth::user()->company_id;
    }

    public function index(Request $request)
    {
        $branchId  = $this->branchId();
        $companyId = $this->companyId();

        // Get products in stock at this branch
        $query = Stock::with(['variant.product'])
            ->where('branch_id', $branchId)
            ->where('quantity', '>', 0)
            ->whereHas('variant.product', fn($q) => $q->where('company_id', $companyId));

        if ($request->filled('search')) {
            $query->whereHas('variant.product', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        $stocks = $query->paginate(30);

        // Pass the default barcode setting so the index page can show current config
        $barcodeSetting = BarcodeSetting::getDefault();

        return view('branch.barcode.index', compact('stocks', 'barcodeSetting'));
    }

    public function print(Request $request)
    {
        $request->validate([
            'variant_ids'   => 'required|array|min:1',
            'variant_ids.*' => 'exists:product_variants,id',
            'copies'        => 'required|integer|min:1|max:100',
        ]);

        $variants = ProductVariant::with('product')
            ->whereIn('id', $request->variant_ids)
            ->whereHas('product', fn($q) => $q->where('company_id', $this->companyId()))
            ->get();

        $copies = $request->copies;

        // Load the active default barcode setting (set by Super Admin)
        $barcodeSetting = BarcodeSetting::getDefault();

        return view('branch.barcode.print', compact('variants', 'copies', 'barcodeSetting'));
    }
}
