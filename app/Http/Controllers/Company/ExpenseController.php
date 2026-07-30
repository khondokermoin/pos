<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    /**
     * Display a listing of expenses for this company.
     */
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;

        $query = Expense::with(['branch', 'createdBy'])
            ->where('company_id', $companyId)
            ->latest();

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('expense_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('expense_date', '<=', $request->date_to);
        }

        // Branch filter
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        $expenses = $query->paginate(15)->withQueryString();

        // Stats
        $totalAllTime = Expense::where('company_id', $companyId)->sum('amount');
        $totalThisMonth = Expense::where('company_id', $companyId)
            ->whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)
            ->sum('amount');
        $totalEntries = Expense::where('company_id', $companyId)->count();

        $branches = Branch::where('company_id', $companyId)->where('status', 'active')->get();

        return view('company.expenses.index', compact(
            'expenses',
            'totalAllTime',
            'totalThisMonth',
            'totalEntries',
            'branches'
        ));
    }

    /**
     * Store a newly created expense.
     */
    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;

        $request->validate([
            'title'        => 'required|string|max:255',
            'amount'       => 'required|numeric|min:0',
            'category'     => 'required|string|max:100',
            'expense_date' => 'required|date',
            'branch_id'    => 'nullable|exists:branches,id',
            'description'  => 'nullable|string|max:1000',
        ]);

        Expense::create([
            'company_id'   => $companyId,
            'branch_id'    => $request->branch_id,
            'title'        => $request->title,
            'amount'       => $request->amount,
            'category'     => $request->category,
            'expense_date' => $request->expense_date,
            'description'  => $request->description,
            'created_by'   => Auth::id(),
        ]);

        return redirect()->route('company.expenses.index')
            ->with('success', 'Expense recorded successfully.');
    }

    /**
     * Update the specified expense.
     */
    public function update(Request $request, Expense $expense)
    {
        if ($expense->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'title'        => 'required|string|max:255',
            'amount'       => 'required|numeric|min:0',
            'category'     => 'required|string|max:100',
            'expense_date' => 'required|date',
            'branch_id'    => 'nullable|exists:branches,id',
            'description'  => 'nullable|string|max:1000',
        ]);

        $expense->update([
            'branch_id'    => $request->branch_id,
            'title'        => $request->title,
            'amount'       => $request->amount,
            'category'     => $request->category,
            'expense_date' => $request->expense_date,
            'description'  => $request->description,
        ]);

        return redirect()->route('company.expenses.index')
            ->with('success', 'Expense updated successfully.');
    }

    /**
     * Remove the specified expense.
     */
    public function destroy(Expense $expense)
    {
        if ($expense->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized action.');
        }

        $expense->delete();

        return redirect()->route('company.expenses.index')
            ->with('success', 'Expense deleted successfully.');
    }
}
