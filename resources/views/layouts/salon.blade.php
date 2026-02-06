@extends('layouts.app')

@section('body')
    @php
        $currencySymbol = \App\Models\Setting::currencySymbol();
    @endphp
    <div class="flex h-screen overflow-hidden">
        @include('components.salon.sidebar')
        <div class="flex-1 flex flex-col min-w-0">
            @if(session()->has('salon_impersonator_email'))
                @php
                    $impersonatorEmail = (string) session('salon_impersonator_email', '');
                    $impersonatorRole = (string) session('salon_impersonator_role', 'admin');
                    $impersonatedEmail = (string) session('salon_user_email', '');
                    $impersonatedRole = (string) session('salon_role', '');
                @endphp
                <div class="bg-amber-50 border-b border-amber-200 px-4 py-2 flex items-center justify-between gap-3">
                    <div class="text-sm text-amber-900 min-w-0">
                        <span class="font-semibold">Impersonating</span>
                        <span class="ml-1">{{ $impersonatedEmail !== '' ? $impersonatedEmail : 'user' }}</span>
                        @if($impersonatedRole !== '')
                            <span class="text-amber-700">({{ $impersonatedRole }})</span>
                        @endif
                        <span class="text-amber-700">—</span>
                        <span class="text-amber-700 truncate">Go back to {{ $impersonatorEmail !== '' ? $impersonatorEmail : 'previous user' }} ({{ $impersonatorRole }})</span>
                    </div>
                    <form method="POST" action="{{ route('salon.impersonation.stop') }}" class="shrink-0">
                        @csrf
                        <button type="submit" class="px-3 py-1.5 text-sm bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition font-medium">
                            Go back
                        </button>
                    </form>
                </div>
            @endif
            @yield('content')
        </div>
    </div>
    @include('components.salon.modal')
    @prepend('scripts')
    <script>
        window.salonJsonBase = window.salonJsonBase || '{{ url("api/salon/data") }}';
        window.salonCurrencySymbol = window.salonCurrencySymbol || @json($currencySymbol);
        window.salonFormatMoney = window.salonFormatMoney || function(amount) {
            var symbol = window.salonCurrencySymbol || '$';
            var num = Number(amount || 0);
            if (!isFinite(num)) num = 0;
            var abs = Math.abs(num).toFixed(2);
            return (num < 0 ? '-' : '') + symbol + abs;
        };
    </script>
    @endprepend
@endsection
