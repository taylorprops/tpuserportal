<x-guest-layout>
    <x-auth-card>

        <x-slot name="logo">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </x-slot>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="text-white">

                <div class="text-bold text-xl mb-2">Employee Login</div>

                <!-- Email Address -->
                <div class="pt-3">

                    <input type="text" class="form-element input lg"
                        id="email"
                        type="email"
                        name="email"
                        :value="old('email')"
                        data-label=""
                        placeholder="Email"
                        required
                        autofocus>
                </div>

                <!-- Password -->
                <div class="mt-4">

                    <input type="password" class="form-element input lg"
                        id="password"
                        name="password"
                        data-label=""
                        placeholder="Password"
                        required autocomplete="current-password">
                </div>

                <!-- Remember Me -->
                <div class="block mt-4">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-primary shadow-sm focus:border-primary-dark focus:ring focus:ring-primary-lightest focus:ring-opacity-50" name="remember">
                        <span class="ml-2 text-sm text-white">{{ __('Remember me') }}</span>
                    </label>
                </div>

                <div class="flex items-center justify-between mt-4">
                    @if (Route::has('password.request'))
                        <a class="light underline text-sm text-white hover:text-gray-50" href="{{ route('password.request') }}">
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif

                    <button type="submit" class="button default lg ml-3">
                        Login <i class="fal fa-sign-in ml-2"></i>
                    </button>

                </div>

            </div>

        </form>

        <!-- Session Status -->
        <x-auth-session-status class="mt-4 bg-white p-2  rounded" :status="session('status')" />

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mt-4 text-red-600 bg-white p-2 border border-red-500 rounded" :errors="$errors" />


    </x-auth-card>
</x-guest-layout>
