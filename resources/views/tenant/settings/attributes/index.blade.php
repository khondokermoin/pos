@extends('layouts.admin_master')

@section('title', 'Attribute Settings')

@section('content')
    <div class="container-fluid px-0">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1">Attribute Settings</h4>
                <p class="text-muted mb-0">Manage product attributes and value options for your tenant.</p>
            </div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAttributeModal">
                <i class="ti ti-plus me-1"></i> Add Attribute
            </button>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card shadow-sm">
            <div class="card-body">
                @if($attributes->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <p class="mb-0">No attributes created yet.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Values</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($attributes as $attribute)
                                    <tr>
                                        <td><strong>{{ $attribute->name }}</strong></td>
                                        <td>
                                            @foreach($attribute->values as $value)
                                                <span class="badge bg-light text-dark me-1">{{ $value->value }}</span>
                                            @endforeach
                                        </td>
                                        <td class="text-end">
                                            <form action="{{ route('tenant.attributes.destroy', $attribute) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="modal fade" id="addAttributeModal" tabindex="-1" aria-labelledby="addAttributeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('tenant.attributes.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="addAttributeModalLabel">Add New Attribute</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Attribute Name</label>
                            <input type="text" class="form-control" name="name" placeholder="e.g. Material" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Values</label>
                            <textarea class="form-control" name="values" rows="4" placeholder="Cotton, Silk, Denim"></textarea>
                            <small class="text-muted">Separate values with commas or new lines.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Attribute</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
