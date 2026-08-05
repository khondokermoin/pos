<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\CashAccount;
use App\Models\CashTransfer;
use App\Models\CashTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CashBookController extends Controller
{
    private function companyId(): int
    {
        return Auth::user()->company_id;
    }

    // ── Cash Accounts ──────────────────────────────────────────────────────

    public function accounts(Request $request)
    {
        $accounts = CashAccount::where('company_id', $this->companyId())
            ->orderBy('name')
            ->get();

        return view('company.cashbook.accounts', compact('accounts'));
    }

    public function storeAccount(Request $request)
    {
        $data = $request->validate([
            'name'            => 'required|string|max:255',
            'type'            => 'required|in:cash,bank,mobile_banking',
            'opening_balance' => 'required|numeric|min:0',
            'notes'           => 'nullable|string|max:500',
        ]);

        $data['company_id']      = $this->companyId();
        $data['current_balance'] = $data['opening_balance'];

        CashAccount::create($data);

        return redirect()->route('company.cashbook.accounts')
            ->with('success', 'Account created successfully.');
    }

    public function destroyAccount(string $id)
    {
        $account = CashAccount::where('company_id', $this->companyId())->findOrFail($id);
        $account->delete();

        return redirect()->route('company.cashbook.accounts')
            ->with('success', 'Account deleted.');
    }

    // ── Account Balances ───────────────────────────────────────────────────

    public function balances(Request $request)
    {
        $accounts     = CashAccount::where('company_id', $this->companyId())->get();
        $totalBalance = $accounts->sum('current_balance');

        return view('company.cashbook.balances', compact('accounts', 'totalBalance'));
    }

    // ── Cash Transfers ─────────────────────────────────────────────────────

    public function transfers(Request $request)
    {
        $transfers = CashTransfer::with(['fromAccount', 'toAccount'])
            ->where('company_id', $this->companyId())
            ->latest()
            ->paginate(20);

        $accounts = CashAccount::where('company_id', $this->companyId())
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('company.cashbook.transfers', compact('transfers', 'accounts'));
    }

    public function storeTransfer(Request $request)
    {
        $companyId = $this->companyId();

        $data = $request->validate([
            'from_account_id' => 'required|exists:cash_accounts,id,company_id,' . $companyId,
            'to_account_id'   => 'required|exists:cash_accounts,id,company_id,' . $companyId . '|different:from_account_id',
            'amount'          => 'required|numeric|min:0.01',
            'transfer_date'   => 'required|date',
            'notes'           => 'nullable|string|max:500',
        ]);

        $from = CashAccount::where('company_id', $companyId)->findOrFail($data['from_account_id']);
        $to   = CashAccount::where('company_id', $companyId)->findOrFail($data['to_account_id']);

        if ($from->current_balance < $data['amount']) {
            return back()->withErrors(['amount' => 'Insufficient balance in source account.'])->withInput();
        }

        DB::transaction(function () use ($data, $companyId, $from, $to) {
            CashTransfer::create(array_merge($data, [
                'company_id' => $companyId,
                'created_by' => Auth::id(),
            ]));

            $from->decrement('current_balance', $data['amount']);
            $to->increment('current_balance', $data['amount']);

            // Ledger entries
            CashTransaction::create([
                'company_id'       => $companyId,
                'cash_account_id'  => $from->id,
                'type'             => 'debit',
                'amount'           => $data['amount'],
                'description'      => 'Transfer to ' . $to->name,
                'transaction_date' => $data['transfer_date'],
            ]);
            CashTransaction::create([
                'company_id'       => $companyId,
                'cash_account_id'  => $to->id,
                'type'             => 'credit',
                'amount'           => $data['amount'],
                'description'      => 'Transfer from ' . $from->name,
                'transaction_date' => $data['transfer_date'],
            ]);
        });

        return redirect()->route('company.cashbook.transfers')
            ->with('success', 'Transfer recorded successfully.');
    }

    // ── Transaction History ────────────────────────────────────────────────

    public function history(Request $request)
    {
        $companyId = $this->companyId();

        $query = CashTransaction::with('account')
            ->where('company_id', $companyId);

        if ($request->filled('account_id')) {
            $query->where('cash_account_id', $request->account_id);
        }
        if ($request->filled('from')) {
            $query->whereDate('transaction_date', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('transaction_date', '<=', $request->to);
        }

        $transactions = $query->latest('transaction_date')->paginate(25);
        $accounts     = CashAccount::where('company_id', $companyId)->orderBy('name')->get();

        return view('company.cashbook.history', compact('transactions', 'accounts'));
    }
}
