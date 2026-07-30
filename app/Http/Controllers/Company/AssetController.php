<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssetController extends Controller
{
    private function companyId(): int
    {
        return Auth::user()->company_id;
    }

    // ── Asset Types ────────────────────────────────────────────────────────

    public function types(Request $request)
    {
        $types = AssetType::where('company_id', $this->companyId())
            ->withCount('assets')
            ->orderBy('name')
            ->get();

        return view('company.assets.types', compact('types'));
    }

    public function storeType(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        $data['company_id'] = $this->companyId();
        AssetType::create($data);

        return redirect()->route('company.assets.types')
            ->with('success', 'Asset type created successfully.');
    }

    public function destroyType(string $id)
    {
        AssetType::where('company_id', $this->companyId())->findOrFail($id)->delete();

        return redirect()->route('company.assets.types')
            ->with('success', 'Asset type deleted.');
    }

    // ── Assets ─────────────────────────────────────────────────────────────

    public function assets(Request $request)
    {
        $companyId = $this->companyId();

        $query = Asset::with('type')->where('company_id', $companyId);

        if ($request->filled('type_id')) {
            $query->where('asset_type_id', $request->type_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $assets = $query->latest()->paginate(20);
        $types  = AssetType::where('company_id', $companyId)->orderBy('name')->get();

        $totalValue = Asset::where('company_id', $companyId)
            ->where('status', 'active')
            ->sum('current_value');

        return view('company.assets.assets', compact('assets', 'types', 'totalValue'));
    }

    public function storeAsset(Request $request)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'asset_type_id'  => 'required|exists:asset_types,id',
            'purchase_date'  => 'required|date',
            'purchase_price' => 'required|numeric|min:0',
            'current_value'  => 'nullable|numeric|min:0',
            'notes'          => 'nullable|string|max:500',
        ]);

        $data['company_id']    = $this->companyId();
        $data['current_value'] = $data['current_value'] ?? $data['purchase_price'];
        Asset::create($data);

        return redirect()->route('company.assets.assets')
            ->with('success', 'Asset added successfully.');
    }

    public function destroyAsset(string $id)
    {
        Asset::where('company_id', $this->companyId())->findOrFail($id)->delete();

        return redirect()->route('company.assets.assets')
            ->with('success', 'Asset deleted.');
    }
}
