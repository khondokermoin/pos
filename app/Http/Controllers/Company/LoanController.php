<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\LoanAuthority;
use App\Models\LoanPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoanController extends Controller
{
    private function companyId(): int
    {
        return Auth::user()->company_id;
    }

    // ── Loan Authorities ───────────────────────────────────────────────────

    public function authorities(Request $request)
    {
        $authorities = LoanAuthority::where('company_id', $this->companyId())
            ->withCount('loans')
            ->orderBy('name')
            ->get();

        return view('company.loans.authorities', compact('authorities'));
    }

    public function storeAuthority(Request $request)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'contact' => 'nullable|string|max:100',
            'notes'   => 'nullable|string|max:500',
        ]);

        $data['company_id'] = $this->companyId();
        LoanAuthority::create($data);

        return redirect()->route('company.loans.authorities')
            ->with('success', 'Loan authority added successfully.');
    }

    public function destroyAuthority(string $id)
    {
        LoanAuthority::where('company_id', $this->companyId())->findOrFail($id)->delete();

        return redirect()->route('company.loans.authorities')
            ->with('success', 'Loan authority deleted.');
    }

    // ── Loans ──────────────────────────────────────────────────────────────

    public function loans(Request $request)
    {
        $loans = Loan::with('authority')
            ->where('company_id', $this->companyId())
            ->latest()
            ->paginate(20);

        $authorities = LoanAuthority::where('company_id', $this->companyId())
            ->orderBy('name')
            ->get();

        return view('company.loans.loans', compact('loans', 'authorities'));
    }

    public function storeLoan(Request $request)
    {
        $data = $request->validate([
            'loan_authority_id' => 'required|exists:loan_authorities,id',
            'amount'            => 'required|numeric|min:0.01',
            'interest_rate'     => 'nullable|numeric|min:0|max:100',
            'loan_date'         => 'required|date',
            'due_date'          => 'nullable|date|after_or_equal:loan_date',
            'notes'             => 'nullable|string|max:500',
        ]);

        $data['company_id'] = $this->companyId();
        Loan::create($data);

        return redirect()->route('company.loans.loans')
            ->with('success', 'Loan recorded successfully.');
    }

    public function destroyLoan(string $id)
    {
        Loan::where('company_id', $this->companyId())->findOrFail($id)->delete();

        return redirect()->route('company.loans.loans')
            ->with('success', 'Loan deleted.');
    }

    // ── Loan Payments ──────────────────────────────────────────────────────

    public function payments(Request $request)
    {
        $companyId = $this->companyId();

        $payments = LoanPayment::with('loan.authority')
            ->where('company_id', $companyId)
            ->latest()
            ->paginate(20);

        $loans = Loan::with('authority')
            ->where('company_id', $companyId)
            ->where('status', '!=', 'paid')
            ->orderBy('loan_date', 'desc')
            ->get();

        return view('company.loans.payments', compact('payments', 'loans'));
    }

    public function storePayment(Request $request)
    {
        $data = $request->validate([
            'loan_id'      => 'required|exists:loans,id',
            'amount'       => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'notes'        => 'nullable|string|max:500',
        ]);

        $data['company_id'] = $this->companyId();
        LoanPayment::create($data);

        // Update loan status if fully paid
        $loan      = Loan::findOrFail($data['loan_id']);
        $totalPaid = $loan->payments()->sum('amount');
        if ($totalPaid >= $loan->amount) {
            $loan->update(['status' => 'paid']);
        }

        return redirect()->route('company.loans.payments')
            ->with('success', 'Loan payment recorded successfully.');
    }
}
