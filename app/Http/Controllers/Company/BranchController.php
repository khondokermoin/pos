<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $companyId = Auth::user()->company_id;

        // শুধুমাত্র বর্তমান কোম্পানির ব্রাঞ্চগুলো আনা
        $branches = Branch::where('company_id', $companyId)
            ->with('manager') // ম্যানেজারের তথ্য লোড করা (যদি User মডেলে relationship থাকে)
            ->latest()
            ->paginate(10);

        return view('company.branches.index', compact('branches'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $companyId = Auth::user()->company_id;

        // ড্রপডাউনের জন্য শুধুমাত্র এই কোম্পানির ইউজারদের আনা
        $managers = User::where('company_id', $companyId)->get();

        return view('company.branches.create', compact('managers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'manager_id' => [
                'nullable',
                // সিকিউরিটি: ম্যানেজার অবশ্যই বর্তমান কোম্পানির ইউজার হতে হবে
                Rule::exists('users', 'id')->where('company_id', Auth::user()->company_id)
            ],
        ]);

        Branch::create([
            'name' => $request->name,
            'address' => $request->address,
            'phone' => $request->phone,
            'email' => $request->email,
            'manager_id' => $request->manager_id,
            'company_id' => Auth::user()->company_id, // ✅ এই লাইনটি থাকা জরুরি
            'status' => 'active',
        ]);

        return redirect()->route('company.branches.index')
            ->with('success', 'Branch created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // সিকিউরিটি: নিশ্চিত করা যে ব্রাঞ্চটি বর্তমান কোম্পানির অধীনে
        $branch = Branch::where('company_id', Auth::user()->company_id)->findOrFail($id);

        return view('company.branches.show', compact('branch'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $branch = Branch::where('company_id', Auth::user()->company_id)->findOrFail($id);
        $managers = User::where('company_id', Auth::user()->company_id)->get();

        return view('company.branches.edit', compact('branch', 'managers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $branch = Branch::where('company_id', Auth::user()->company_id)->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'manager_id' => [
                'nullable',
                Rule::exists('users', 'id')->where('company_id', Auth::user()->company_id)
            ],
            'status' => 'required|in:active,inactive',
        ]);

        $branch->update([
            'name' => $request->name,
            'address' => $request->address,
            'phone' => $request->phone,
            'email' => $request->email,
            'manager_id' => $request->manager_id,
            'status' => $request->status,
        ]);

        return redirect()->route('company.branches.index')
            ->with('success', 'Branch updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $branch = Branch::where('company_id', Auth::user()->company_id)->findOrFail($id);

        // (Optional) আপনি চাইলে ডিলিট করার আগে চেক করতে পারেন যে এই ব্রাঞ্চে কোনো ইউজার বা প্রোডাক্ট আছে কিনা
        // if ($branch->users()->count() > 0) {
        //     return back()->with('error', 'Cannot delete a branch that has assigned users.');
        // }

        $branch->delete();

        return redirect()->route('company.branches.index')
            ->with('success', 'Branch deleted successfully!');
    }

    // ==========================================
    // 1-Click Branch Login (Impersonation)
    // ==========================================

    /**
     * Log in as the Manager of the given branch.
     * Stores the Company Admin's ID in session so they can return later.
     */
    public function impersonate(string $id)
    {
        $companyAdmin = Auth::user();
        $branch = Branch::where('company_id', $companyAdmin->company_id)->findOrFail($id);

        // Guard: prevent nested impersonation
        if (Session::has('branch_impersonator_id')) {
            return redirect()->route('company.branches.index')
                ->with('error', 'You are already logged into a branch. Please return to the Company panel first.');
        }

        // Find the Manager assigned to this branch
        // Strategy 1: Use the branch's manager_id field
        $manager = null;
        if ($branch->manager_id) {
            $manager = User::where('id', $branch->manager_id)
                ->where('company_id', $companyAdmin->company_id)
                ->first();
        }

        // Strategy 2: Find any user with 'Manager' role assigned to this branch
        if (! $manager) {
            $manager = User::where('branch_id', $branch->id)
                ->where('company_id', $companyAdmin->company_id)
                ->whereHas('roles', function ($query) {
                    $query->where('name', 'Manager');
                })
                ->first();
        }

        // No manager found — show a helpful error
        if (! $manager) {
            return redirect()->route('company.branches.index')
                ->with('error', 'Please assign a Manager to "' . $branch->name . '" before logging in as that branch.');
        }

        // Store the Company Admin's ID so we can restore them later
        Session::put('branch_impersonator_id', $companyAdmin->id);
        Session::put('branch_impersonator_name', $companyAdmin->name);

        // Log in as the branch Manager
        Auth::login($manager);

        return redirect()->route('branch.dashboard')
            ->with('success', 'You are now viewing as ' . $manager->name . ' (' . $branch->name . '). Click "Back to Company" to return.');
    }

    /**
     * Leave branch impersonation and return to the Company Admin account.
     */
    public function leaveBranchImpersonation(Request $request)
    {
        $impersonatorId = Session::pull('branch_impersonator_id');
        Session::forget('branch_impersonator_name');

        if (! $impersonatorId) {
            return redirect()->route('company.dashboard')
                ->with('error', 'You are not currently impersonating any branch.');
        }

        $originalAdmin = User::find($impersonatorId);

        if (! $originalAdmin) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('error', 'Your original Company Admin account could not be restored. Please log in again.');
        }

        Auth::logout();
        Auth::login($originalAdmin);
        $request->session()->regenerate();

        return redirect()->route('company.branches.index')
            ->with('success', 'You have successfully returned to your Company Admin account.');
    }
}
