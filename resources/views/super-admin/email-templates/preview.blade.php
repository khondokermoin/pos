@extends('layouts.super-admin')
@section('title', 'Preview: ' . $emailTemplate->name)
@section('content')

    <div class="row mb-3 align-items-center">
        <div class="col-sm-6">
            <h4 class="page-title mb-0"><i class="ti ti-eye me-2 text-primary"></i>Email Template Preview</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('superadmin.email-templates.index') }}">Email Templates</a></li>
                    <li class="breadcrumb-item active">Preview</li>
                </ol>
            </nav>
        </div>
        <div class="col-sm-6 text-sm-end">
            <a href="{{ route('superadmin.email-templates.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="ti ti-arrow-left me-1"></i>Back to Templates
            </a>
        </div>
    </div>

    <div class="row">
        {{-- Left: Template Info --}}
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="ti ti-info-circle me-2"></i>Template Info</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted">Name</td>
                            <td><strong>{{ $emailTemplate->name }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Slug</td>
                            <td><code class="small">{{ $emailTemplate->slug }}</code></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status</td>
                            <td>
                                @if ($emailTemplate->is_active)
                                    <span class="badge bg-success-subtle text-success">Active</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger">Inactive</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Created</td>
                            <td>{{ $emailTemplate->created_at->format('d M Y') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Variables Panel --}}
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0 small"><i class="ti ti-variable me-2"></i>Available Variables</h5>
                </div>
                <div class="card-body">
                    @if ($emailTemplate->variables && count($emailTemplate->variables) > 0)
                        <div class="d-flex flex-wrap gap-1">
                            @foreach ($emailTemplate->variables as $var)
                                <code class="badge bg-light text-dark border small">{{"{{"}}{{ $var }}{{"}}"}}</code>
                            @endforeach
                        </div>
                        <p class="text-muted small mt-2 mb-0">
                            <i class="ti ti-info-circle me-1"></i>
                            Use <code>{{"{{"}}variable{{"}}"}}</code> syntax in your template body and subject.
                        </p>
                    @else
                        <p class="text-muted small mb-0">No variables defined for this template.</p>
                    @endif
                </div>
            </div>

            {{-- Rendered Subject --}}
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0 small"><i class="ti ti-mail me-2"></i>Rendered Subject</h5>
                </div>
                <div class="card-body">
                    <div class="p-2 bg-light rounded border small font-monospace">
                        {{ $renderedSubject }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: Rendered Body Preview --}}
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="ti ti-mail me-2"></i>Rendered Body Preview</h5>
                    <span class="badge bg-info-subtle text-info">Sample Data</span>
                </div>
                <div class="card-body p-0">
                    {{-- Rendered HTML Preview in iframe for isolation --}}
                    <div class="p-3 border-bottom">
                        <h6 class="text-muted small mb-2"><i class="ti ti-eye me-1"></i>Rendered Output (with sample values)</h6>
                        <div class="border rounded bg-white" style="min-height: 300px; overflow: hidden;">
                            <iframe
                                srcdoc="{{ $renderedBody }}"
                                style="width:100%; min-height:400px; border:none;"
                                sandbox="allow-same-origin"
                                title="Email Preview"
                            ></iframe>
                        </div>
                    </div>

                    {{-- Raw HTML Source --}}
                    <div class="p-3">
                        <h6 class="text-muted small mb-2"><i class="ti ti-code me-1"></i>Raw HTML Source</h6>
                        <pre class="bg-dark text-light rounded p-3 mb-0" style="font-size:11px; max-height:350px; overflow-y:auto; white-space:pre-wrap; word-break:break-all;"><code>{{ $emailTemplate->body }}</code></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
