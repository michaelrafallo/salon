{{-- Salon header: optional page header content can be yielded from child views --}}
@if(isset($slot) && trim($slot) !== '')
    <header>{{ $slot }}</header>
@endif
