@extends('layouts.super-admin')
@section('title', 'Preview: ' . $invoiceTemplate->name)
@section('content')

    <div class="row mb-3 align-items-center">
        <div class="col-sm-6">
            <h4 class="page-title mb-0"><i class="ti ti-eye me-2 text-primary"></i>Template Preview</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('superadmin.invoice-templates.index') }}">Invoice
                            Templates</a></li>
                    <li class="breadcrumb-item active">Preview</li>
                </ol>
            </nav>
        </div>
        <div class="col-sm-6 text-sm-end">
            <a href="{{ route('superadmin.invoice-templates.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="ti ti-arrow-left me-1"></i>Back to Templates
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="ti ti-info-circle me-2"></i>Template Info</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted">Name</td>
                            <td><strong>{{ $invoiceTemplate->name }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Type</td>
                            <td>
                                <span class="badge bg-info-subtle text-info">{{ $invoiceTemplate->getTypeLabel() }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Default</td>
                            <td>
                                @if ($invoiceTemplate->is_default)
                                    <span class="badge bg-success"><i class="ti ti-star-filled me-1"></i>Yes</span>
                                @else
                                    <span class="badge bg-secondary">No</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status</td>
                            <td>
                                @if ($invoiceTemplate->is_active)
                                    <span class="badge bg-success-subtle text-success">Active</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger">Inactive</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Created</td>
                            <td>{{ $invoiceTemplate->created_at->format('d M Y') }}</td>
                        </tr>
                    </table>

                    <hr>

                    <div class="d-grid gap-2">
                        @if (!$invoiceTemplate->is_default)
                            <form method="POST"
                                action="{{ route('superadmin.invoice-templates.set-default', $invoiceTemplate) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-success btn-sm w-100">
                                    <i class="ti ti-star me-1"></i>Set as Default
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('superadmin.invoice-templates.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="ti ti-edit me-1"></i>Edit Template
                        </a>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0 small"><i class="ti ti-variable me-2"></i>Available Variables</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-1">
                        @foreach (['{{ company_name }}', '{{ invoice_no }}', '{{ date }}', '{{ customer_name }}', '{{ items }}', '{{ subtotal }}', '{{ tax }}', '{{ discount }}', '{{ total }}', '{{ paid }}', '{{ balance }}', '{{ cashier }}'] as $var)
                            <code class="badge bg-light text-dark border small">{{ $var }}</code>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="ti ti-receipt me-2"></i>HTML Preview</h5>
                    <span class="badge bg-secondary">{{ $invoiceTemplate->getTypeLabel() }}</span>
                </div>
                <div class="card-body p-0">
                    @if ($invoiceTemplate->html_content)
                        {{-- Rendered Preview --}}
                        <div class="p-3 border-bottom">
                            <h6 class="text-muted small mb-2"><i class="ti ti-eye me-1"></i>Rendered Output</h6>
                            <div class="border rounded p-3 bg-white"
                                style="min-height:200px; font-family: monospace; font-size:13px;">
                                {!! $invoiceTemplate->html_content !!}
                            </div>
                        </div>
                        {{-- Raw HTML Source --}}
                        <div class="p-3">
                            <h6 class="text-muted small mb-2"><i class="ti ti-code me-1"></i>HTML Source</h6>
                            <pre class="bg-dark text-light rounded p-3 mb-0" style="font-size:12px; max-height:400px; overflow-y:auto;"><code>{{ $invoiceTemplate->html_content }}</code></pre>
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="ti ti-file-off d-block mb-3" style="font-size:3rem;opacity:.4;"></i>
                            <h5>No HTML Content</h5>
                            <p class="small">This template has no HTML content yet.</p>
                            <a href="{{ route('superadmin.invoice-templates.index') }}" class="btn btn-primary btn-sm">
                                <i class="ti ti-edit me-1"></i>Add Content
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection
