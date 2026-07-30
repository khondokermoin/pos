<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
    /**
     * Display a listing of suppliers for this company.
     */
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;

        $query = Supplier::where('company_id', $companyId)->latest();

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('contact_person', 'like', "%{$search}%");
            });
        }

        $suppliers = $query->paginate(15)->withQueryString();

        // Stats
        $totalCount    = Supplier::where('company_id', $companyId)->count();
        $activeCount   = Supplier::where('company_id', $companyId)->where('status', 'active')->count();
        $inactiveCount = Supplier::where('company_id', $companyId)->where('status', 'inactive')->count();

        return view('company.suppliers.index', compact(
            'suppliers',
            'totalCount',
            'activeCount',
            'inactiveCount'
        ));
    }

    /**
     * Store a newly created supplier.
     */
    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;

        $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'nullable|email|max:255',
            'phone'          => 'nullable|string|max:20',
            'address'        => 'nullable|string|max:500',
            'contact_person' => 'nullable|string|max:255',
            'notes'          => 'nullable|string',
        ]);

        Supplier::create([
            'company_id'     => $companyId,
            'name'           => $request->name,
            'email'          => $request->email,
            'phone'          => $request->phone,
            'address'        => $request->address,
            'contact_person' => $request->contact_person,
            'notes'          => $request->notes,
            'status'         => 'active',
        ]);

        return redirect()->route('company.suppliers.index')
            ->with('success', 'Supplier added successfully.');
    }

    /**
     * Update the specified supplier.
     */
    public function update(Request $request, Supplier $supplier)
    {
        // Security: ensure supplier belongs to this company
        if ($supplier->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'nullable|email|max:255',
            'phone'          => 'nullable|string|max:20',
            'address'        => 'nullable|string|max:500',
            'contact_person' => 'nullable|string|max:255',
            'notes'          => 'nullable|string',
            'status'         => 'required|in:active,inactive',
        ]);

        $supplier->update([
            'name'           => $request->name,
            'email'          => $request->email,
            'phone'          => $request->phone,
            'address'        => $request->address,
            'contact_person' => $request->contact_person,
            'notes'          => $request->notes,
            'status'         => $request->status,
        ]);

        return redirect()->route('company.suppliers.index')
            ->with('success', 'Supplier updated successfully.');
    }

    /**
     * Remove the specified supplier.
     */
    public function destroy(Supplier $supplier)
    {
        // Security: ensure supplier belongs to this company
        if ($supplier->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized action.');
        }

        // Prevent deletion if supplier has purchase history
        if ($supplier->purchases()->count() > 0) {
            return redirect()->route('company.suppliers.index')
                ->with('error', 'Cannot delete supplier with purchase history. You can mark them as Inactive instead.');
        }

        $supplier->delete();

        return redirect()->route('company.suppliers.index')
            ->with('success', 'Supplier deleted successfully.');
    }
}
