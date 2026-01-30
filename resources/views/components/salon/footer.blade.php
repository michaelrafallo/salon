{{-- Salon footer: closing structure is in layout; use for optional footer content --}}
@if(isset($slot) && trim($slot) !== '')
    <footer>{{ $slot }}</footer>
@endif
