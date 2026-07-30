<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\StoreUserRequest;
use App\Http\Requests\SuperAdmin\UpdateUserRequest;
use App\Models\User;
use App\Models\Company;
use App\Models\Branch;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['roles', 'company', 'branch'])
            ->latest()
            ->paginate(15);
            
        return view('super-admin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        $companies = Company::all();
        $branches = Branch::all();
        
        return view('super-admin.users.create', compact('roles', 'companies', 'branches'));
    }

    public function store(StoreUserRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'company_id' => $request->company_id,
            'branch_id' => $request->branch_id,
        ]);

        // Assign Roles
        $user->syncRoles($request->roles);

        return redirect()->route('superadmin.users.index')
            ->with('success', 'User created successfully and roles assigned.');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $userRoles = $user->roles->pluck('name')->toArray();
        $companies = Company::all();
        $branches = Branch::all();
        
        return view('super-admin.users.edit', compact('user', 'roles', 'userRoles', 'companies', 'branches'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'company_id' => $request->company_id,
            'branch_id' => $request->branch_id,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        $user->syncRoles($request->roles);

        return redirect()->route('superadmin.users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return redirect()->route('superadmin.users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        $user->delete();
        return redirect()->route('superadmin.users.index')
            ->with('success', 'User deleted successfully.');
    }
}