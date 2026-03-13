<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="asset-url" content="{{ asset('') }}">
    @php $appBusinessName = \App\Models\Setting::query()->where('option_key', 'business_name')->value('option_value') ?: 'Nail Salon POS'; @endphp
    <title>{{ isset($pageTitle) ? $pageTitle . ' - ' . $appBusinessName : $appBusinessName }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body.font-poppins { font-family: 'Poppins', sans-serif; }
        a, button, [onclick], [role="button"], select, label[for], input[type="checkbox"], input[type="radio"], .cursor-pointer { cursor: pointer; }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50 font-poppins">
    @yield('body')
    <script src="{{ asset('js/salon-api.js') }}"></script>
    @stack('scripts')
</body>
</html>
