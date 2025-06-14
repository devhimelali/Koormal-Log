@if ($errors->any())
    @foreach ($errors->all() as $error)
        <script>
            notify('error', "{{ $error }}");
        </script>
    @endforeach
@endif
<script>
    @if (Session::has('success'))
        notify('success', "{{ session('success') }}");
    @elseif (Session::has('error'))
        notify('error', "{{ Session::get('error') }}");
    @elseif (Session::has('warning'))
        notify('warning', "{{ Session::get('warning') }}");
    @elseif (Session::has('info'))
        notify('info', "{{ Session::get('info') }}");
    @endif

    @foreach (session('toasts', collect())->toArray() as $toast)
        const options = {
            title: '{{ $toast['title'] ?? '' }}',
            message: '{{ $toast['message'] ?? 'No message provided' }}',
            position: '{{ $toast['position'] ?? 'topRight' }}',
        };
        show('{{ $toast['type'] ?? 'info' }}', options);
    @endforeach

    function notify(type, msg, position = 'topRight') {
        toastr[type](msg);
    }

    function show(type, options) {
        if (['info', 'success', 'warning', 'error'].includes(type)) {
            toastr[type](options);
        } else {
            toastr.show(options);
        }
    }

    function implementAutoAjaxLoading() {
        $(document).ajaxStart(function() {
            $('#ajaxLoaderOverlay').fadeIn(200);
        });

        $(document).ajaxStop(function() {
            $('#ajaxLoaderOverlay').fadeOut(200);
        });
    }
</script>
