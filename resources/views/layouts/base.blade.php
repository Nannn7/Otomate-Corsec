@php
    use Nwidart\Modules\Module;
@endphp
<!doctype html>
<html class="h-full" data-theme="true" data-theme-mode="light" lang="en">

<head>
    <meta charset="utf-8">
    <title>{{ config('app.name', 'Corsec App') }}</title>
    <base href="/">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <link rel="stylesheet" href="{{ asset('vendor/dropzone/dropzone.min.css') }}" type="text/css" />

    @vite(Module::getAssets())
    @stack('styles')
</head>

<body style="zoom:80%!important" class="flex h-full metronic sidebar-fixed header-fixed bg-[#fefefe] dark:bg-coal-500">

    @if (session('error'))
        <em class="hidden toastr" data-type="error" data-message=" {{ session('error') }}"></em>
    @endif

    @if (session('info'))
        <em class="hidden toastr" data-type="info" data-message=" {{ session('info') }}"></em>
    @endif

    @if (session('warning'))
        <em class="hidden toastr" data-type="warning" data-message=" {{ session('warning') }}"></em>
    @endif

    @if (session('success'))
        <em class="hidden toastr" data-type="success" data-message=" {{ session('success') }}"></em>
    @endif

    @if ($errors->any())
        <em class="hidden toastr" data-type="error"
            data-message="Form belum bisa diproses. Periksa kolom yang ditandai merah."></em>
        <script>
            window.corsecValidationErrors = @json($errors->messages());
        </script>
    @endif


    <!--begin::Theme mode setup on page load-->
    <script>
        const defaultThemeMode = 'system'; // light|dark|system
        let themeMode;

        if (document.documentElement) {
            if (localStorage.getItem('theme')) {
                themeMode = localStorage.getItem('theme');
            } else if (document.documentElement.hasAttribute('data-theme-mode')) {
                themeMode = document.documentElement.getAttribute('data-theme-mode');
            } else {
                themeMode = defaultThemeMode;
            }

            if (themeMode === 'system') {
                themeMode = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }

            document.documentElement.classList.add(themeMode);
        }
    </script>
    <!--end::Theme mode setup on page load-->
    <script src="{{ asset('vendor/filerobot-image-editor/filerobot-image-editor.min.js') }}"></script>
    <script src="{{ asset('vendor/dropzone/dropzone.min.js') }}"></script>

    @yield('main')
    @stack('scripts')


</body>

</html>
