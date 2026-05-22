@php
    use Illuminate\Support\Facades\Route;

    $route      = explode('.',Route::currentRouteName());
	$modules    = file_get_contents(dirname(__FILE__, 4) . '/modules_statuses.json');
    $module     = json_decode($modules);
@endphp

@extends('layouts.base', ['module' => $module])

@section('main')
    <div class="flex grow">
        @include('layouts.sidebar')
        <div class="wrapper flex grow flex-col">
            @include('layouts.header')
            <main class="grow content pt-5" id="content" role="content">
                <!-- begin: container -->
                <div class="container-fixed" id="content_container">
                </div>
                <!-- end: container -->
                <!-- begin: container -->
                <div class="container-fluid">
                    @if (session('success') || session('error') || session('warning') || session('info') || $errors->any())
                        <div class="mb-5 grid gap-3">
                            @foreach (['success' => 'success', 'error' => 'danger', 'warning' => 'warning', 'info' => 'info'] as $sessionKey => $alertType)
                                @if (session($sessionKey))
                                    <div class="alert alert-{{ $alertType }}">
                                        {{ session($sessionKey) }}
                                    </div>
                                @endif
                            @endforeach

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <div class="font-semibold">Form belum bisa diproses.</div>
                                    <div class="mt-1">Periksa kolom yang ditandai merah dan lengkapi sesuai pesan error.</div>
                                    <ul class="mt-2 list-disc pl-5">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    @endif

                    @yield('content')
                </div>
                <!-- end: container -->
            </main>
            @include('layouts.footer')
        </div>
    </div>
@endsection
