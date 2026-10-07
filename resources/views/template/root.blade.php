@extends('layouts.app')

@push('styles')
    @stack('styles')
@endpush

@section('content')
    @yield('content')
@endsection

@push('scripts')
    @stack('scripts')
@endpush
