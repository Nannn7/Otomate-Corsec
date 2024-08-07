@php
    use Illuminate\Support\Facades\Route;

    $route      = explode('.',Route::currentRouteName());
	$modules    = file_get_contents(dirname(__FILE__, 4) . '/modules_statuses.json');
    $module     = json_decode($modules);
@endphp

@extends('layouts.base', ['module' => $module])

@section('main')
    @yield('content')
@endsection
