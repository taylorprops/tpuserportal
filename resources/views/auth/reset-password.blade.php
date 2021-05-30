<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </x-slot>

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <div class="text-default text-lg mb-2">Reset Password</div>

            <!-- Password Reset Token -->
            <input type="hidden" name="token" value="{{ $request -> route('token') }}">

            <!-- Email Address -->
            <div class="mt-5">

                <x-elements.input
                    :size="'md'"
                    id="email"
                    type="email"
                    name="email"
                    :value="old('email', $request -> email)"
                    data-label="Email"
                    placeholder="Email"
                    required
                    readonly />

            </div>

            <!-- Password -->
            <div class="mt-4">

                <x-elements.input
                    :size="'md'"
                    id="password"
                    type="password"
                    name="password"
                    data-label="Password"
                    placeholder="Password"
                    autofocus
                    required />

            </div>

            <!-- Confirm Password -->
            <div class="mt-4">

                <x-elements.input
                    :size="'md'"
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    data-label="Confirm Password"
                    placeholder="Confirm Password"
                    required />

            </div>

            <div class="flex items-center justify-end mt-4">
                <x-button type="submit">
                    {{ __('Reset Password') }}
                </x-button>
            </div>
        </form>
    </x-auth-card>
</x-guest-layout>
