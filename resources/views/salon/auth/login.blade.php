@extends('layouts.app')

@php
    $businessName = \App\Models\Setting::query()->where('option_key', 'business_name')->value('option_value') ?: 'Nail Salon POS';
    $businessLogo = \App\Models\Setting::query()->where('option_key', 'business_logo')->value('option_value');
    $logoUrl = $businessLogo ? asset('storage/' . $businessLogo) : null;
@endphp

@section('body')
    <div class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div class="text-center">
                <div class="flex justify-center mb-4">
                    @if($logoUrl)
                        <img src="{{ $logoUrl }}" alt="{{ $businessName }}" class="h-auto object-contain" style="max-width:180px">
                    @else
                        <div class="w-16 h-16 bg-[#003047] rounded-lg flex items-center justify-center">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                            </svg>
                        </div>
                    @endif
                </div>
                <p class="text-gray-600">Sign in to your account</p>
            </div>

            <div class="bg-white rounded-lg shadow-lg border border-gray-200 p-8">
                <form id="loginForm" class="space-y-6" action="{{ route('salon.login.post') }}" method="POST">
                    @csrf
                    @if($errors->any())
                        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                            {{ $errors->first() }}
                        </div>
                    @endif
                    <div>
                        <label for="login" class="block text-sm font-medium text-gray-700 mb-2">Username or Email</label>
                        <input type="text" id="login" name="login" value="{{ old('login') }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="Enter your username or email">
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <input type="password" id="password" name="password" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="Enter your password">
                    </div>
                    <div class="flex items-center justify-between">
                        <label class="flex items-center">
                            <input type="checkbox" name="remember" class="w-4 h-4 text-[#003047] border-gray-300 rounded focus:ring-[#003047]">
                            <span class="ml-2 text-sm text-gray-600">Remember me</span>
                        </label>
                        <a href="{{ route('salon.forgot-password') }}" class="text-sm text-[#003047] hover:text-[#002535] font-medium">Forgot password?</a>
                    </div>
                    <button type="submit" class="w-full px-4 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95 focus:outline-none focus:ring-2 focus:ring-[#003047] focus:ring-offset-2">Sign In</button>
                </form>
            </div>

            <p class="text-center text-sm text-gray-500">&copy; {{ date('Y') }} {{ $businessName }}. All rights reserved.</p>
        </div>
    </div>

    @push('scripts')
    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            var btn = this.querySelector('button[type="submit"]');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<svg class="w-5 h-5 animate-spin inline-block mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>Signing in...';
            }
        });
    </script>
    @endpush
@endsection
