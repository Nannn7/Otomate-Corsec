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
                    @yield('content')
                </div>
                <!-- end: container -->
            </main>
            @include('layouts.footer')
        </div>
    </div>
@endsection
