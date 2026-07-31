@extends('layouts.super-admin')
@section('title', 'Email Templates')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Email Templates</h4>
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Email Templates</li>
                    </ol>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="ti ti-circle-check me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="ti ti-alert-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <!-- Create Form -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="ti ti-plus me-2"></i>Add Email Template</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('superadmin.email-templates.store') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Template Name <span class="text-danger">*</span></label>
                                <input type="text" name="name"
                                    class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                    placeholder="e.g. Welcome Email" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Subject <span class="text-danger">*</span></label>
                                <input type="text" name="subject"
                                    class="form-control @error('subject') is-invalid @enderror" value="{{ old('subject') }}"
                                    placeholder="e.g. Welcome to {app_name}!" required>
                                @error('subject')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Body (HTML) <span class="text-danger">*</span></label>
                                <textarea name="body" class="form-control @error('body') is-invalid @enderror" rows="10"
                                    placeholder="<p>Dear {customer_name},</p>..." required>{{ old('body') }}</textarea>
                                @error('body')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Available Variables</label>
                                <input type="text" name="variables" class="form-control" value="{{ old('variables') }}"
                                    placeholder="customer_name, app_name, order_id (comma separated)">
                                <div class="form-text">Enter variable names separated by commas.</div>
                            </div>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
                                <label class="form-check-label">Active</label>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="ti ti-device-floppy me-1"></i> Save Template
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Templates List -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-mail me-2 text-primary"></i>All Email Templates
                            <span class="badge bg-secondary ms-2">{{ $templates->total() }}</span>
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Name</th>
                                        <th>Subject</th>
                                        <th>Variables</th>
                                        <th>Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($templates as $template)
                                        <tr>
                                            <td>
                                                <strong>{{ $template->name }}</strong><br>
                                                <small class="text-muted">{{ $template->slug }}</small>
                                            </td>
                                            <td>{{ Str::limit($template->subject, 40) }}</td>
                                            <td>
                                                @if ($template->variables)
                                                    @foreach (array_slice($template->variables, 0, 3) as $var)
                                                        <span class="badge bg-light text-dark me-1">
                                                            <code>@{{ $var }}</code>
                                                        </span>
                                                    @endforeach
                                                    @if (count($template->variables) > 3)
                                                        <span class="badge bg-secondary">
                                                            +{{ count($template->variables) - 3 }}
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($template->is_active)
                                                    <span class="badge bg-success-subtle text-success">Active</span>
                                                @else
                                                    <span class="badge bg-danger-subtle text-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <button type="button" class="btn btn-sm btn-info me-1"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#previewModal{{ $template->id }}" title="Preview">
                                                    <i class="ti ti-eye"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-warning me-1"
                                                    data-bs-toggle="modal" data-bs-target="#editEmail{{ $template->id }}">
                                                    <i class="ti ti-edit"></i>
                                                </button>
                                                <form method="POST"
                                                    action="{{ route('superadmin.email-templates.destroy', $template) }}"
                                                    class="d-inline" onsubmit="return confirm('Delete this template?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="ti ti-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>

                                        <!-- Preview Modal -->
                                        <div class="modal fade" id="previewModal{{ $template->id }}" tabindex="-1">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-info text-white">
                                                        <h5 class="modal-title">
                                                            <i class="ti ti-eye me-2"></i>Preview: {{ $template->name }}
                                                        </h5>
                                                        <button type="button" class="btn-close btn-close-white"
                                                            data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3 p-3 bg-light rounded">
                                                            <strong>Subject:</strong> {{ $template->subject }}
                                                        </div>
                                                        @if ($template->variables)
                                                            <div class="mb-3">
                                                                <strong>Variables:</strong>
                                                                @foreach ($template->variables as $var)
                                                                    <span
                                                                        class="badge bg-primary-subtle text-primary me-1">
                                                                        <code>@{{ $var }}</code>
                                                                    </span>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                        @php
                                                            $previewSampleData = ['year' => date('Y')];
                                                            foreach ($template->variables ?? [] as $var) {
                                                                if ($var === 'year') {
                                                                    continue;
                                                                }
                                                                $previewSampleData[$var] = '[' . strtoupper(str_replace('_', ' ', $var)) . ']';
                                                            }
                                                            $previewRenderedBody = $template->render($previewSampleData);
                                                        @endphp
                                                        <div class="mb-2">
                                                            <small class="text-muted">
                                                                <i class="ti ti-info-circle me-1"></i>
                                                                The preview is rendered in an isolated iframe so email CSS
                                                                cannot leak into the admin layout.
                                                            </small>
                                                        </div>
                                                        <div class="border rounded bg-white" style="overflow:hidden;">
                                                            <iframe
                                                                srcdoc="{{ $previewRenderedBody }}"
                                                                sandbox="allow-same-origin"
                                                                title="Email preview for {{ $template->name }}"
                                                                style="width:100%; min-height:360px; border:none; display:block;"
                                                            ></iframe>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <a href="{{ route('superadmin.email-templates.preview', $template) }}"
                                                            class="btn btn-info btn-sm" target="_blank">
                                                            <i class="ti ti-external-link me-1"></i>Full Preview
                                                        </a>
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Edit Modal -->
                                        <div class="modal fade" id="editEmail{{ $template->id }}" tabindex="-1">
                                            <div class="modal-dialog modal-xl">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Edit: {{ $template->name }}</h5>
                                                        <button type="button" class="btn-close"
                                                            data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form method="POST"
                                                        action="{{ route('superadmin.email-templates.update', $template) }}">
                                                        @csrf @method('PUT')
                                                        <div class="modal-body">
                                                            <div class="row">
                                                                <div class="col-md-6 mb-3">
                                                                    <label class="form-label">Name</label>
                                                                    <input type="text" name="name"
                                                                        class="form-control"
                                                                        value="{{ $template->name }}" required>
                                                                </div>
                                                                <div class="col-md-6 mb-3">
                                                                    <label class="form-label">Subject</label>
                                                                    <input type="text" name="subject"
                                                                        class="form-control"
                                                                        value="{{ $template->subject }}" required>
                                                                </div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Body (HTML)</label>
                                                                <textarea name="body" class="form-control" rows="12">{{ $template->body }}</textarea>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Variables (comma
                                                                    separated)</label>
                                                                <input type="text" name="variables"
                                                                    class="form-control"
                                                                    value="{{ implode(', ', $template->variables ?? []) }}">
                                                            </div>
                                                            <div class="form-check form-switch">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="is_active" value="1"
                                                                    {{ $template->is_active ? 'checked' : '' }}>
                                                                <label class="form-check-label">Active</label>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-primary">Update</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted">
                                                <i class="ti ti-mail-off fs-24 d-block mb-2"></i>
                                                No email templates yet.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if ($templates->hasPages())
                        <div class="card-footer">{{ $templates->links() }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
