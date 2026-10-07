@extends('layouts.app')

@push('styles')
    @inertiaHead
@endpush

@section('content')
    <div class="kt-container-fixed">
        @inertia
    </div>
@endsection

@push('scripts')
    <script src="{{ mix('/js/app.js') }}" defer></script>
@endpush
