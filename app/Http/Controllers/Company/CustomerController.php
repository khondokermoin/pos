<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    /**
     * Display a listing of customers for this company.
     */
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;

        $query = Customer::where('company_id', $companyId)->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $customers = $query->paginate(15)->withQueryString();

        // Stats
        $totalCount = Customer::where('company_id', $companyId)->count();
        $newThisMonth = Customer::where('company_id', $companyId)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return view('company.customers.index', compact('customers', 'totalCount', 'newThisMonth'));
    }

    /**
     * Store a newly created customer.
     */
    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;

        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'nullable|email|max:255|unique:customers,email,NULL,id,company_id,' . $companyId,
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'notes'   => 'nullable|string',
        ]);

        Customer::create([
            'company_id' => $companyId,
            'name'       => $request->name,
            'email'      => $request->email,
            'phone'      => $request->phone,
            'address'    => $request->address,
            'notes'      => $request->notes,
        ]);

        return redirect()->route('company.customers.index')
            ->with('success', 'Customer added successfully.');
    }

    /**
     * Update the specified customer.
     */
    public function update(Request $request, Customer $customer)
    {
        if ($customer->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized action.');
        }

        $companyId = Auth::user()->company_id;

        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'nullable|email|max:255|unique:customers,email,' . $customer->id . ',id,company_id,' . $companyId,
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'notes'   => 'nullable|string',
        ]);

        $customer->update([
            'name'    => $request->name,
            'email'   => $request->email,
            'phone'   => $request->phone,
            'address' => $request->address,
            'notes'   => $request->notes,
        ]);

        return redirect()->route('company.customers.index')
            ->with('success', 'Customer updated successfully.');
    }

    /**
     * Remove the specified customer.
     */
    public function destroy(Customer $customer)
    {
        if ($customer->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized action.');
        }

        // Prevent deletion if customer has sales history
        if ($customer->sales()->count() > 0) {
            return redirect()->route('company.customers.index')
                ->with('error', 'Cannot delete customer with sales history.');
        }

        $customer->delete();

        return redirect()->route('company.customers.index')
            ->with('success', 'Customer deleted successfully.');
    }
}
