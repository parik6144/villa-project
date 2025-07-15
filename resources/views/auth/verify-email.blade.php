<x-guest-layout>
    <div class="text-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-4">Congratulations!</h1>
    </div>

    <div class="mb-4 text-sm text-gray-600 text-center">
        <p class="mb-4">
            Thanks for signing up! Your account has been created successfully. Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.
        </p>
        
        <p class="mb-4">
            If you do not see the email within a few minutes, be sure to check your spam or junk folder.
        </p>
        
        <p class="mb-4 text-orange-600 font-medium">
            Expire time for confirmation url 24 hours.
        </p>
        
        <p class="text-lg font-semibold text-blue-600">
            Welcome aboard!
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-600 text-center">
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
    @endif

    <div class="mt-6 flex items-center justify-center space-x-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button>
                    {{ __('Resend Verification Email') }}
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</x-guest-layout>
