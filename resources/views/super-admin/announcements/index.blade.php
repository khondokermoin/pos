@extends('layouts.super-admin')
@section('title', 'Announcements')
@section('content')

    <div class="row mb-3 align-items-center">
        <div class="col-sm-6">
            <h4 class="page-title mb-0"><i class="ti ti-speakerphone me-2 text-primary"></i>Announcements</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Announcements</li>
                </ol>
            </nav>
        </div>
        <div class="col-sm-6 text-sm-end">
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createModal">
                <i class="ti ti-plus me-1"></i>New Announcement
            </button>
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

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="ti ti-speakerphone me-2 text-primary"></i>All Announcements
                <span class="badge bg-secondary ms-2">{{ $announcements->total() }}</span>
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">#</th>
                            <th>Title</th>
                            <th>Audience</th>
                            <th>Status</th>
                            <th>Published</th>
                            <th>Expires</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($announcements as $announcement)
                            <tr>
                                <td class="ps-3">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $announcement->title }}</div>
                                    <small class="text-muted">{{ Str::limit($announcement->body, 60) }}</small>
                                </td>
                                <td>
                                    @php
                                        $audienceColors = [
                                            'all' => 'primary',
                                            'company' => 'info',
                                            'branch' => 'warning',
                                        ];
                                        $ac = $audienceColors[$announcement->audience] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $ac }}-subtle text-{{ $ac }}">
                                        {{ ucfirst($announcement->audience) }}
                                    </span>
                                </td>
                                <td>
                                    @if ($announcement->is_active)
                                        <span class="badge bg-success-subtle text-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <small>
                                        {{ $announcement->published_at ? $announcement->published_at->format('d M Y') : '—' }}
                                    </small>
                                </td>
                                <td>
                                    <small>
                                        @if ($announcement->expires_at)
                                            @if ($announcement->expires_at->isPast())
                                                <span class="text-danger">{{ $announcement->expires_at->format('d M Y') }}
                                                    (Expired)
                                                </span>
                                            @else
                                                {{ $announcement->expires_at->format('d M Y') }}
                                            @endif
                                        @else
                                            <span class="text-muted">Never</span>
                                        @endif
                                    </small>
                                </td>
                                <td class="text-end pe-3">
                                    <button type="button" class="btn btn-sm btn-soft-warning me-1" data-bs-toggle="modal"
                                        data-bs-target="#editModal{{ $announcement->id }}" title="Edit">
                                        <i class="ti ti-edit"></i>
                                    </button>
                                    <form action="{{ route('superadmin.announcements.destroy', $announcement) }}"
                                        method="POST" class="d-inline"
                                        onsubmit="return confirm('Delete this announcement?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-soft-danger" title="Delete">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            {{-- Edit Modal --}}
                            <div class="modal fade" id="editModal{{ $announcement->id }}" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title"><i class="ti ti-edit me-2"></i>Edit Announcement</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form method="POST"
                                            action="{{ route('superadmin.announcements.update', $announcement) }}">
                                            @csrf @method('PUT')
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Title <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" name="title" class="form-control"
                                                        value="{{ $announcement->title }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Body <span
                                                            class="text-danger">*</span></label>
                                                    <textarea name="body" class="form-control" rows="4" required>{{ $announcement->body }}</textarea>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4 mb-3">
                                                        <label class="form-label fw-semibold">Audience</label>
                                                        <select name="audience" class="form-select">
                                                            <option value="all"
                                                                {{ $announcement->audience === 'all' ? 'selected' : '' }}>
                                                                All</option>
                                                            <option value="company"
                                                                {{ $announcement->audience === 'company' ? 'selected' : '' }}>
                                                                Company Admins</option>
                                                            <option value="branch"
                                                                {{ $announcement->audience === 'branch' ? 'selected' : '' }}>
                                                                Branch Staff</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label class="form-label fw-semibold">Published At</label>
                                                        <input type="datetime-local" name="published_at"
                                                            class="form-control"
                                                            value="{{ $announcement->published_at ? $announcement->published_at->format('Y-m-d\TH:i') : '' }}">
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label class="form-label fw-semibold">Expires At</label>
                                                        <input type="datetime-local" name="expires_at"
                                                            class="form-control"
                                                            value="{{ $announcement->expires_at ? $announcement->expires_at->format('Y-m-d\TH:i') : '' }}">
                                                    </div>
                                                </div>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" name="is_active"
                                                        value="1" {{ $announcement->is_active ? 'checked' : '' }}>
                                                    <label class="form-check-label">Active</label>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="ti ti-device-floppy me-1"></i>Update
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="ti ti-speakerphone d-block mb-3" style="font-size:3rem;opacity:.4;"></i>
                                    <h5>No Announcements Yet</h5>
                                    <p class="small mb-3">Create your first announcement to notify tenants.</p>
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#createModal">
                                        <i class="ti ti-plus me-1"></i>Create Announcement
                                    </button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($announcements->hasPages())
                <div class="px-3 py-2 border-top">
                    {{ $announcements->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Create Modal --}}
    <div class="modal fade" id="createModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="ti ti-speakerphone me-2"></i>New Announcement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('superadmin.announcements.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title"
                                class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}"
                                placeholder="e.g. System Maintenance on Sunday" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Body <span class="text-danger">*</span></label>
                            <textarea name="body" class="form-control @error('body') is-invalid @enderror" rows="5"
                                placeholder="Write your announcement message here..." required>{{ old('body') }}</textarea>
                            @error('body')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Audience</label>
                                <select name="audience" class="form-select">
                                    <option value="all" {{ old('audience') === 'all' ? 'selected' : '' }}>All</option>
                                    <option value="company" {{ old('audience') === 'company' ? 'selected' : '' }}>Company
                                        Admins</option>
                                    <option value="branch" {{ old('audience') === 'branch' ? 'selected' : '' }}>Branch
                                        Staff</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Published At</label>
                                <input type="datetime-local" name="published_at" class="form-control"
                                    value="{{ old('published_at', now()->format('Y-m-d\TH:i')) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Expires At</label>
                                <input type="datetime-local" name="expires_at" class="form-control"
                                    value="{{ old('expires_at') }}">
                                <div class="form-text">Leave blank for no expiry.</div>
                            </div>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                {{ old('is_active', '1') ? 'checked' : '' }}>
                            <label class="form-check-label">Active (visible to users immediately)</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-send me-1"></i>Publish Announcement
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var modal = new bootstrap.Modal(document.getElementById('createModal'));
                modal.show();
            });
        </script>
    @endif

@endsection
