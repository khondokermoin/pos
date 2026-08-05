<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * The roles a Company Admin is allowed to assign to staff.
     * IMPORTANT: These must exactly match the role names in the `roles` table
     * and the middleware guards on branch routes (role:Manager|Salesman).
     */
    protected array $allowedRoles = [
        'Manager',   // ✅ Matches `role:Manager|Salesman` middleware — can access branch panel
        'Salesman',  // ✅ Matches `role:Manager|Salesman` middleware — can access branch panel
    ];

    /**
     * Display a listing of all staff users for this company.
     */
    public function index()
    {
        $companyId = Auth::user()->company_id;

        $users = User::where('company_id', $companyId)
            ->with(['roles', 'branch'])
            ->latest()
            ->paginate(15);

        $branches = Branch::where('company_id', $companyId)->get();

        // Pass plan limits to the view for the UI to show usage
        $company      = Auth::user()->company;
        $subscription = $company?->subscription;
        $plan         = $subscription?->plan ?? $company?->plan;
        $userLimit    = $plan?->user_limit ?? null;
        $currentCount = $users->total();

        return view('company.users.index', compact('users', 'branches', 'userLimit', 'currentCount'));
    }

    public function create()
    {
        $companyAdmin = Auth::user();
        $company      = $companyAdmin->company;

        // ── Subscription User Limit Check ──────────────────────────────────
        // Count existing staff (exclude the Company Admin themselves)
        $currentStaffCount = User::where('company_id', $companyAdmin->company_id)
            ->where('id', '!=', $companyAdmin->id)
            ->count();

        $subscription = $company?->subscription;
        $plan         = $subscription?->plan ?? $company?->plan;
        $userLimit    = $plan?->user_limit ?? null;

        if ($userLimit !== null && $currentStaffCount >= $userLimit) {
            return redirect()->route('company.users.index')
                ->with('error', "User limit reached! Your current plan allows a maximum of {$userLimit} staff members. Please upgrade your plan to add more.");
        }

        $branches = Branch::where('company_id', $companyAdmin->company_id)->get();

        return view('company.users.create', compact('branches', 'userLimit', 'currentStaffCount'));
    }

    public function store(Request $request)
    {
        $companyAdmin = Auth::user();

        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|min:6|confirmed',
            'role'      => 'required|string|in:' . implode(',', $this->allowedRoles),
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        // ── Double-check user limit on store (race condition guard) ─────────
        $company      = $companyAdmin->company;
        $subscription = $company?->subscription;
        $plan         = $subscription?->plan ?? $company?->plan;
        $userLimit    = $plan?->user_limit ?? null;

        $currentStaffCount = User::where('company_id', $companyAdmin->company_id)
            ->where('id', '!=', $companyAdmin->id)
            ->count();

        if ($userLimit !== null && $currentStaffCount >= $userLimit) {
            return redirect()->route('company.users.index')
                ->with('error', "User limit reached! Your plan allows a maximum of {$userLimit} staff members.");
        }

        // ── Security: ensure branch belongs to this company ─────────────────
        if ($request->branch_id) {
            $branchBelongsToCompany = Branch::where('id', $request->branch_id)
                ->where('company_id', $companyAdmin->company_id)
                ->exists();

            if (! $branchBelongsToCompany) {
                abort(403, 'Invalid branch selected.');
            }
        }

        $user = User::create([
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'company_id' => $companyAdmin->company_id,
            'branch_id'  => $request->branch_id,
        ]);

        // Assign the role — must match exactly what the branch middleware expects
        $user->assignRole($request->role);

        return redirect()->route('company.users.index')
            ->with('success', "Staff member '{$user->name}' created with role '{$request->role}' successfully!");
    }

    /**
     * Assign or change a user's role.
     */
    public function assignRole(Request $request, User $user)
    {
        // Security: only allow managing users within the same company
        if ($user->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'role' => 'required|string|in:' . implode(',', $this->allowedRoles),
        ]);

        $user->syncRoles([$request->role]);

        return redirect()->back()->with('success', 'Role updated for ' . $user->name . ' successfully.');
    }

    /**
     * Remove the specified user from the company.
     */
    public function destroy(User $user)
    {
        if ($user->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized action.');
        }

        // Prevent self-deletion
        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('company.users.index')->with('success', 'User removed successfully.');
    }
}
