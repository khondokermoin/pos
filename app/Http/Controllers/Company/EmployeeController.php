<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use App\Models\SalaryIncrement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    private function companyId(): int
    {
        return Auth::user()->company_id;
    }

    public function index(Request $request)
    {
        $companyId = $this->companyId();

        $query = Employee::with('department')->where('company_id', $companyId);

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%')
                    ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }

        $employees   = $query->orderBy('name')->paginate(20);
        $departments = Department::where('company_id', $companyId)->orderBy('name')->get();

        return view('company.employees.index', compact('employees', 'departments'));
    }

    public function create()
    {
        $departments = Department::where('company_id', $this->companyId())
            ->orderBy('name')
            ->get();

        return view('company.employees.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'nullable|email|max:255',
            'phone'         => 'nullable|string|max:20',
            'department_id' => 'nullable|exists:departments,id',
            'designation'   => 'nullable|string|max:255',
            'join_date'     => 'required|date',
            'salary'        => 'required|numeric|min:0',
        ]);

        $data['company_id'] = $this->companyId();
        Employee::create($data);

        return redirect()->route('company.employees.index')
            ->with('success', 'Employee added successfully.');
    }

    public function show(string $id)
    {
        $employee = Employee::with(['department', 'increments', 'payrolls'])
            ->where('company_id', $this->companyId())
            ->findOrFail($id);

        return view('company.employees.show', compact('employee'));
    }

    public function edit(string $id)
    {
        $employee    = Employee::where('company_id', $this->companyId())->findOrFail($id);
        $departments = Department::where('company_id', $this->companyId())->orderBy('name')->get();

        return view('company.employees.create', compact('employee', 'departments'));
    }

    public function update(Request $request, string $id)
    {
        $employee = Employee::where('company_id', $this->companyId())->findOrFail($id);

        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'nullable|email|max:255',
            'phone'         => 'nullable|string|max:20',
            'department_id' => 'nullable|exists:departments,id',
            'designation'   => 'nullable|string|max:255',
            'join_date'     => 'required|date',
            'salary'        => 'required|numeric|min:0',
            'status'        => 'required|in:active,inactive,terminated',
        ]);

        $employee->update($data);

        return redirect()->route('company.employees.index')
            ->with('success', 'Employee updated successfully.');
    }

    public function destroy(string $id)
    {
        Employee::where('company_id', $this->companyId())->findOrFail($id)->delete();

        return redirect()->route('company.employees.index')
            ->with('success', 'Employee deleted successfully.');
    }

    // ── Salary Increments ──────────────────────────────────────────────────

    public function increments(Request $request)
    {
        $companyId = $this->companyId();

        $increments = SalaryIncrement::with('employee')
            ->where('company_id', $companyId)
            ->latest('effective_date')
            ->paginate(20);

        $employees = Employee::where('company_id', $companyId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('company.employees.increments', compact('increments', 'employees'));
    }

    public function storeIncrement(Request $request)
    {
        $data = $request->validate([
            'employee_id'    => 'required|exists:employees,id',
            'amount'         => 'required|numeric|min:0.01',
            'effective_date' => 'required|date',
            'reason'         => 'nullable|string|max:500',
        ]);

        $data['company_id'] = $this->companyId();
        SalaryIncrement::create($data);

        // Update employee salary
        $employee = Employee::findOrFail($data['employee_id']);
        $employee->increment('salary', $data['amount']);

        return redirect()->route('company.employees.increments')
            ->with('success', 'Salary increment recorded successfully.');
    }
}
