@extends('layouts.admin_master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex justify-content-between align-items-center">
                <h4 class="page-title">Create New Platform User</h4>
                <a href="{{ route('superadmin.users.index') }}" class="btn btn-secondary">
                    <i class="ti ti-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('superadmin.users.store') }}" method="POST" id="userCreateForm">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Assign Company <span class="text-danger">*</span></label>
                                <select name="company_id" id="company_id" class="form-select @error('company_id') is-invalid @enderror" required>
                                    <option value="">-- Select Company --</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>
                                            {{ $company->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Leave blank only for Super Admin.</small>
                                @error('company_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Assign Branch <span class="text-danger">*</span></label>
                                <select name="branch_id" id="branch_id" class="form-select @error('branch_id') is-invalid @enderror">
                                    <option value="">-- Select Branch --</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                            {{ $branch->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted text-branch-hint">Only for Branch Manager / Cashier.</small>
                                @error('branch_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label d-block mb-2">Assign Roles <span class="text-danger">*</span></label>
                            <div class="row">
                                @foreach($roles as $role)
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input role-checkbox @error('roles') is-invalid @enderror" 
                                               type="checkbox" 
                                               name="roles[]" 
                                               value="{{ $role->name }}" 
                                               id="role_{{ $role->id }}" 
                                               data-role-name="{{ strtolower($role->name) }}"
                                               {{ in_array($role->name, old('roles', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="role_{{ $role->id }}">
                                            {{ $role->name }}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @error('roles') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </form>
                        
                        <!-- Form Submit Buttons -->
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('superadmin.users.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Create User</button each="form">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ✅ JavaScript to Handle Dynamic Branch Visibility --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleCheckboxes = document.querySelectorAll('.role-checkbox');
    const branchSelect = document.getElementById('branch_id');
    const branchHint = document.querySelector('.text-branch-hint');

    function toggleBranchField() {
        let isBranchRole = false;
        
        // Check if any selected role is a branch-level role
        roleCheckboxes.forEach(checkbox => {
            if (checkbox.checked) {
                const roleName = checkbox.getAttribute('data-role-name');
                if (roleName.includes('branch') || roleName.includes('cashier') || roleName.includes('staff')) {
                    isBranchRole = true;
                }
            }
        });

        // If it's a branch role, show the branch dropdown. Otherwise, hide and clear it.
        if (isBranchRole) {
            branchSelect.disabled = false;
            branchSelect.classList.remove('d-none');
            branchHint.classList.remove('d-none');
            branchSelect.setAttribute('required', 'required');
        } else {
            branchSelect.disabled = true;
            branchSelect.classList.add('d-none');
            branchHint.classList.add('d-none');
            branchSelect.removeAttribute('required');
            branchSelect.value = ''; // Clear the value so it submits as null
        }
    }

    // Attach event listeners to all role checkboxes
    roleCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', toggleBranchField);
    });

    // Run once on page load to set initial state
    toggleBranchField();
});
</script>
@endsection