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
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Forgot Password</h2>
                <p class="text-gray-600">Enter your email to reset your password</p>
            </div>

            <div class="bg-white rounded-lg shadow-lg border border-gray-200 p-8">
                <form id="forgotPasswordForm" class="space-y-6">
                    <div id="errorMessage" class="hidden bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm"></div>
                    <div id="successMessage" class="hidden bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm"></div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                        <input type="email" id="email" name="email" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="Enter your email address">
                        <p class="mt-2 text-sm text-gray-500">We'll send you a link to reset your password if your email is registered.</p>
                    </div>
                    <button type="submit" id="submitBtn" class="w-full px-4 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95 focus:outline-none focus:ring-2 focus:ring-[#003047] focus:ring-offset-2">Send Reset Link</button>
                    <div class="text-center">
                        <a href="{{ route('salon.login') }}" class="text-sm text-[#003047] hover:text-[#002535] font-medium inline-flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                            Back to Login
                        </a>
                    </div>
                </form>
            </div>

            <p class="text-center text-sm text-gray-500">© {{ date('Y') }} Nail Salon POS. All rights reserved.</p>
        </div>
    </div>

    @push('scripts')
    <script>
        document.getElementById('forgotPasswordForm').addEventListener('submit', function(e) {
            e.preventDefault();
            var email = document.getElementById('email').value;
            var errorDiv = document.getElementById('errorMessage');
            var successDiv = document.getElementById('successMessage');
            var submitBtn = document.getElementById('submitBtn');
            errorDiv.classList.add('hidden');
            successDiv.classList.add('hidden');
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                errorDiv.textContent = 'Please enter a valid email address.';
                errorDiv.classList.remove('hidden');
                return;
            }
            submitBtn.disabled = true;
            submitBtn.textContent = 'Sending...';
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            setTimeout(function() {
                successDiv.innerHTML = '<div class="flex items-start gap-3"><svg class="w-5 h-5 text-green-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg><div><p class="font-medium">Password reset link sent!</p><p class="text-sm mt-1">We\'ve sent a password reset link to <strong>' + email + '</strong>. Please check your email inbox.</p></div></div>';
                successDiv.classList.remove('hidden');
                document.getElementById('forgotPasswordForm').style.display = 'none';
                setTimeout(function() { window.location.href = '{{ route('salon.login') }}'; }, 3000);
            }, 1500);
        });
    </script>
    @endpush
@endsection
