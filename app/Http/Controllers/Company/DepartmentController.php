<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DepartmentController extends Controller
{
    private function companyId(): int
    {
        return Auth::user()->company_id;
    }

    public function index(Request $request)
    {
        $departments = Department::where('company_id', $this->companyId())
            ->withCount('employees')
            ->orderBy('name')
            ->get();

        return view('company.departments.index', compact('departments'));
    }

    public function create()
    {
        return view('company.departments.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        $data['company_id'] = $this->companyId();
        Department::create($data);

        return redirect()->route('company.departments.index')
            ->with('success', 'Department created successfully.');
    }

    public function edit(string $id)
    {
        $department = Department::where('company_id', $this->companyId())->findOrFail($id);

        return view('company.departments.create', compact('department'));
    }

    public function update(Request $request, string $id)
    {
        $department = Department::where('company_id', $this->companyId())->findOrFail($id);

        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        $department->update($data);

        return redirect()->route('company.departments.index')
            ->with('success', 'Department updated successfully.');
    }

    public function destroy(string $id)
    {
        Department::where('company_id', $this->companyId())->findOrFail($id)->delete();

        return redirect()->route('company.departments.index')
            ->with('success', 'Department deleted successfully.');
    }
}
