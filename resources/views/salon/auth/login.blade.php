@extends('layouts.app')

@section('body')
    <div class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div class="text-center">
                <div class="flex justify-center mb-4">
                    <div class="w-16 h-16 bg-[#003047] rounded-lg flex items-center justify-center">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                    </div>
                </div>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Nail Salon POS</h2>
                <p class="text-gray-600">Sign in to your account</p>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="flex-1">
                        <h3 class="text-sm font-semibold text-blue-900 mb-2">Test Login Credentials</h3>
                        <div class="space-y-1 text-sm text-blue-800">
                            <p><span class="font-medium">Email:</span> admin@salon.com</p>
                            <p><span class="font-medium">Password:</span> admin123</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-lg border border-gray-200 p-8">
                <form id="loginForm" class="space-y-6" action="{{ route('salon.login.post') }}" method="POST">
                    @csrf
                    <div id="errorMessage" class="hidden bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm"></div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                        <input type="email" id="email" name="email" required value="admin@salon.com" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="admin@salon.com">
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

            <p class="text-center text-sm text-gray-500">© {{ date('Y') }} Nail Salon POS. All rights reserved.</p>
        </div>
    </div>

    @push('scripts')
    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const errorDiv = document.getElementById('errorMessage');
            if (email === 'admin@salon.com' && password === 'admin123') {
                this.submit();
            } else {
                errorDiv.textContent = 'Invalid email or password. Please try again.';
                errorDiv.classList.remove('hidden');
                setTimeout(function() { errorDiv.classList.add('hidden'); }, 5000);
            }
        });
    </script>
    @endpush
@endsection
