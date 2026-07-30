@if (auth()->user()->hasRole('Super Admin'))
    @include('partials.sidebars.super_admin_sidebar')
@elseif(auth()->user()->hasRole('Company Admin'))
    @include('partials.sidebars.company_sidebar')
@elseif(auth()->user()->hasAnyRole(['Manager', 'Salesman']))
    @include('partials.sidebars.branch_sidebar')
@endif
