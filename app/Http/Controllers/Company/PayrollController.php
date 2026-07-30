<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Payroll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PayrollController extends Controller
{
    private function companyId(): int
    {
        return Auth::user()->company_id;
    }

    public function index(Request $request)
    {
        $companyId = $this->companyId();
        $month     = $request->input('month', now()->format('Y-m'));

        $payrolls = Payroll::with('employee.department')
            ->where('company_id', $companyId)
            ->where('month', $month)
            ->orderBy('created_at', 'desc')
            ->get();

        $employees = Employee::where('company_id', $companyId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $totalNet    = $payrolls->sum('net_salary');
        $totalPaid   = $payrolls->where('status', 'paid')->sum('net_salary');
        $totalPending = $payrolls->where('status', 'pending')->sum('net_salary');

        return view('company.payroll.index', compact(
            'payrolls',
            'employees',
            'month',
            'totalNet',
            'totalPaid',
            'totalPending'
        ));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'month' => 'required|date_format:Y-m',
        ]);

        $companyId = $this->companyId();
        $month     = $request->month;

        $employees = Employee::where('company_id', $companyId)
            ->where('status', 'active')
            ->get();

        $generated = 0;
        foreach ($employees as $employee) {
            // Skip if already generated for this month
            $exists = Payroll::where('company_id', $companyId)
                ->where('employee_id', $employee->id)
                ->where('month', $month)
                ->exists();

            if (!$exists) {
                Payroll::create([
                    'company_id'   => $companyId,
                    'employee_id'  => $employee->id,
                    'month'        => $month,
                    'basic_salary' => $employee->salary,
                    'bonus'        => 0,
                    'deduction'    => 0,
                    'net_salary'   => $employee->salary,
                    'status'       => 'pending',
                ]);
                $generated++;
            }
        }

        return redirect()->route('company.payroll.index', ['month' => $month])
            ->with('success', "Payroll generated for {$month}. {$generated} records created.");
    }

    public function markPaid(Request $request, string $id)
    {
        $payroll = Payroll::where('company_id', $this->companyId())->findOrFail($id);
        $payroll->update(['status' => 'paid', 'paid_at' => now()->toDateString()]);

        return redirect()->route('company.payroll.index', ['month' => $payroll->month])
            ->with('success', 'Payroll marked as paid.');
    }

    public function payslip(string $id)
    {
        $payroll = Payroll::with('employee.department')
            ->where('company_id', $this->companyId())
            ->findOrFail($id);

        return view('company.payroll.payslip', compact('payroll'));
    }

    public function destroy(string $id)
    {
        $payroll = Payroll::where('company_id', $this->companyId())->findOrFail($id);
        $month   = $payroll->month;
        $payroll->delete();

        return redirect()->route('company.payroll.index', ['month' => $month])
            ->with('success', 'Payroll entry deleted successfully.');
    }
}
