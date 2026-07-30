<script>
    toastr.options = {
        closeButton: true,
        progressBar: true,
        newestOnTop: true,
        preventDuplicates: true,
        positionClass: "toast-top-right",
        timeOut: 5000,
        extendedTimeOut: 1000,
        showDuration: 300,
        hideDuration: 300,
        showMethod: "fadeIn",
        hideMethod: "fadeOut"
    };

    @if (session('success'))
        toastr.success(@json(session('success')), 'Success');
    @endif

    @if (session('info'))
        toastr.info(@json(session('info')), 'Information');
    @endif

    @if (session('warning'))
        toastr.warning(@json(session('warning')), 'Warning');
    @endif

    @if (session('error'))
        toastr.error(@json(session('error')), 'Error');
    @endif

    @if ($errors->any())
        @foreach ($errors->all() as $error)
            toastr.error(@json($error), 'Validation Error');
        @endforeach
    @endif
</script>
