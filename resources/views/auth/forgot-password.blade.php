<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </x-slot>

        <div class="mb-4 text-sm text-white">
            {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
        </div>



        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <!-- Email Address -->
            <div>

                <input type="text" class="form-element input lg"
                    id="email"
                    type="email"
                    name="email"
                    :value="old('email')"
                    data-label=""
                    placeholder="Email"
                    required autofocus>

            </div>

            <div class="flex items-center justify-between mt-4">
                <a href="/login" class="light text-white">Back to Login</a>
                <button type="submit" class="button default lg">Email Reset Link <i class="fa fa-share ml-2"></i></button>
            </div>
        </form>

        <!-- Session Status -->
        <x-auth-session-status class="mt-4 bg-white p-2 rounded" :status="session('status')" />

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mt-4 text-red-600 bg-white p-2 border border-red-500 rounded" :errors="$errors" />

    </x-auth-card>
</x-guest-layout>
