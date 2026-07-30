@extends('layouts.admin_master')

@section('title', 'Categories')

@push('styles')
    {{-- ✅ DataTables CSS --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

    <style>
        /* ========================================
           1. SEARCH BOX STYLING
           ======================================== */
        .category-search-wrapper {
            position: relative;
            max-width: 300px;
            width: 100%;
        }

        .category-search-wrapper .input-group {
            background-color: var(--bs-body-bg) !important;
            border: 1px solid var(--bs-border-color, rgba(148, 163, 184, 0.4)) !important;
            border-radius: 0.5rem !important;
            overflow: hidden;
            transition: all 0.25s ease !important;
        }

        .category-search-wrapper .input-group:focus-within {
            border-color: rgba(25, 135, 84, 0.5) !important;
            box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.15) !important;
        }

        .category-search-wrapper .input-group-text {
            background-color: transparent !important;
            border: 0 !important;
            color: var(--bs-body-color) !important;
            opacity: 0.7;
        }

        .category-search-wrapper .form-control {
            background-color: transparent !important;
            border: 0 !important;
        }

        .category-search-wrapper .form-control:focus {
            box-shadow: none !important;
        }

        .search-clear-btn {
            background: transparent !important;
            border: 0 !important;
            color: var(--bs-secondary-color) !important;
            padding: 0 0.75rem !important;
            display: none;
            cursor: pointer;
            transition: color 0.2s ease !important;
        }

        .search-clear-btn:hover {
            color: #198754 !important;
        }

        .search-clear-btn.show {
            display: block !important;
        }

        .search-counter {
            font-size: 0.8rem;
            color: var(--bs-secondary-color);
            margin-left: 0.5rem;
            display: none;
        }

        .search-counter.show {
            display: inline-block;
        }

        /* ========================================
           2. DATATABLES CUSTOM STYLING
           ======================================== */
        #categories-table_wrapper {
            margin: 0 !important;
            padding: 0 !important;
        }

        #categories-table_wrapper .row {
            margin: 0 !important;
        }

        #categories-table {
            margin: 0 !important;
        }

        #categories-table thead th {
            color: var(--bs-emphasis-color, var(--bs-body-color)) !important;
            vertical-align: middle;
        }

        #categories-table td.dtr-control {
            text-align: center;
            cursor: pointer;
        }

        /* ========================================
           3. ACTION BUTTONS
           ======================================== */
        .action-btn-group .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
    </style>
@endpush

@section('content')
    <div class="mb-3 row align-items-center">
        <div class="col-sm-6">
            <h4 class="page-title mb-0">
                <i class="ti ti-list me-2 text-primary"></i> Categories
            </h4>
        </div>
        <div class="col-sm-6 text-sm-end">
            <nav aria-label="breadcrumb" class="d-inline-block me-3">
                <ol class="breadcrumb justify-content-end mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('company.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Categories</li>
                </ol>
            </nav>
            <a href="{{ route('company.categories.create') }}" class="btn btn-primary">
                <i class="ti ti-plus me-1"></i> Add New Category
            </a>
        </div>
    </div>

    <!-- Search Filter -->
    <div class="row mb-3">
        <div class="col-sm-6 d-flex align-items-center">
            <form method="GET" action="{{ route('company.categories.index') }}" class="d-flex align-items-center w-100">
                <div class="category-search-wrapper flex-grow-1">
                    <div class="input-group">
                        <button type="submit" class="input-group-text border-0 bg-transparent" title="Search">
                            <i class="ti ti-search"></i>
                        </button>
                        <input type="text" name="search" id="categorySearch" class="form-control"
                            value="{{ request('search') }}" placeholder="Search by name..." autocomplete="off">

                        @if (request('search'))
                            <a href="{{ route('company.categories.index') }}"
                                class="search-clear-btn show d-flex align-items-center justify-content-center text-decoration-none"
                                title="Clear search">
                                <i class="ti ti-x"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>

            @if (request('search') && $categories->count() > 0)
                <span class="search-counter show ms-2">
                    ({{ method_exists($categories, 'total') ? $categories->total() : $categories->count() }} found)
                </span>
            @endif
        </div>
    </div>

    <!-- Categories Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-centered w-100 dt-responsive mb-0" id="categories-table">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 20px;"></th> <!-- Responsive control column -->
                            <th style="width: 60px;" class="text-center">SN</th>
                            <th>Name</th>
                            <th style="width: 120px;" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $category)
                            <tr>
                                <td></td> <!-- Blank Responsive control cell -->
                                
                                <td class="text-center">
                                    @if (method_exists($categories, 'currentPage'))
                                        {{ ($categories->currentPage() - 1) * $categories->perPage() + $loop->iteration }}
                                    @else
                                        {{ $loop->iteration }}
                                    @endif
                                </td>
                                
                                <td class="fw-semibold">{{ $category->name }}</td>
                                
                                <td class="text-center">
                                    <div class="btn-group action-btn-group" role="group">
                                        <a href="{{ route('company.categories.edit', $category) }}" 
                                           class="btn btn-sm btn-outline-info" title="Edit" data-bs-toggle="tooltip">
                                            <i class="ti ti-edit"></i>
                                        </a>
                                        <form action="{{ route('company.categories.destroy', $category) }}" 
                                              method="POST" class="d-inline" 
                                              onsubmit="return confirm('Are you sure you want to delete this category?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete" data-bs-toggle="tooltip">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-5">
                                    <i class="ti ti-folder-off d-block mb-3" style="font-size: 3rem; opacity: 0.5;"></i>
                                    <h5 class="fw-semibold text-body">No Categories Found</h5>
                                    <p class="mb-2 small text-body-secondary">
                                        @if (request('search'))
                                            No categories found matching "<strong>{{ request('search') }}</strong>".
                                        @else
                                            You haven't created any categories yet.
                                        @endif
                                    </p>
                                    @if (!request('search'))
                                        <a href="{{ route('company.categories.create') }}" class="btn btn-sm btn-primary mt-2">
                                            <i class="ti ti-plus me-1"></i> Add Your First Category
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if (method_exists($categories, 'hasPages') && $categories->hasPages())
                <div class="mt-3 d-flex justify-content-center p-3">
                    {{ $categories->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize Bootstrap Tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Initialize DataTables
            const table = $('#categories-table').DataTable({
                order: [],
                columnDefs: [
                    {
                        orderable: false,
                        className: 'dtr-control',
                        targets: [0],
                        width: '20px',
                        responsivePriority: 10000
                    },
                    {
                        orderable: false,
                        targets: [1],
                        width: '60px',
                        responsivePriority: 1
                    },
                    {
                        orderable: true,
                        targets: [2],
                        responsivePriority: 2
                    },
                    {
                        orderable: false,
                        targets: [3],
                        responsivePriority: 3
                    }
                ],
                responsive: {
                    details: {
                        type: 'inline',
                        target: 'td.dtr-control'
                    }
                },
                paging: false, 
                searching: false, 
                info: false,
                lengthChange: false,
                autoWidth: false,
                dom: 't'
            });

            const $searchInput = $('#categorySearch');

            // Keyboard shortcuts for search
            $(document).on('keydown', function(e) {
                if (e.key === '/' && !$(e.target).is('input, textarea')) {
                    e.preventDefault();
                    $searchInput.focus();
                }
                if (e.key === 'Escape' && $searchInput.is(':focus')) {
                    if ($searchInput.val() !== '') {
                        $searchInput.val('');
                        $searchInput.closest('form').submit();
                    } else {
                        $searchInput.blur();
                    }
                }
            });
        });
    </script>
@endpush