@extends('layouts.app')

@section('body')
    <div class="flex h-screen overflow-hidden">
        @include('components.salon.sidebar')
        <div class="flex-1 flex flex-col min-w-0">
            @yield('content')
        </div>
    </div>
    @include('components.salon.modal')
    @push('scripts')
    <script>window.salonJsonBase = window.salonJsonBase || '{{ url("json") }}';</script>
    @endpush
@endsection
