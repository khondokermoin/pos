@extends('layouts.admin_master')
@section('title', 'Business Types')
@section('content')
    <div class="row mb-2 mt-3">
        <div class="col-sm-6">
            <h4 class="page-title">Business Types</h4>
        </div>
        <div class="col-sm-6 text-sm-end">
            <a href="{{ route('superadmin.business-types.create') }}" class="btn btn-primary btn-sm"><i class="ti ti-plus"></i>
                Add New</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($businessTypes as $type)
                        <tr>
                            <td>{{ $type->name }}</td>
                            <td><code>{{ $type->slug }}</code></td>
                            <td><span
                                    class="badge bg-{{ $type->is_active ? 'success' : 'secondary' }}">{{ $type->is_active ? 'Active' : 'Inactive' }}</span>
                            </td>
                            <td class="text-end">
                                <form action="{{ route('superadmin.business-types.destroy', $type) }}" method="POST"
                                    class="d-inline" onsubmit="return confirm('Are you sure?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="ti ti-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">No business types found. Please add one.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $businessTypes->links() }}
        </div>
    </div>
@endsection
