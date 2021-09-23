<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </x-slot>

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="text-default text-lg mb-2">Register Account</div>

            <!-- Name -->
            <div class="pt-5">
                <input type="text" class="form-element input md"
                    id="name"
                    type="text"
                    name="name"
                    :value="old('name')"
                    data-label="Name"
                    placeholder=""
                    required readonly>

            </div>

            <!-- Email Address -->
            <div class="mt-4">

                <input type="text" class="form-element input md"
                    id="email"
                    type="email"
                    name="email"
                    :value="old('email')"
                    data-label="Email"
                    placeholder=""
                    required readonly>

            </div>

            <!-- Password -->
            <div class="mt-4">

                <input type="password" class="form-element input md"
                    id="password"
                    name="password"
                    data-label="Password"
                    placeholder=""
                    required autofocus autocomplete="new-password">

            </div>

            <!-- Confirm Password -->
            <div class="mt-4">

                <input type="password" class="form-element input md"
                    id="password_confirmation"
                    name="password_confirmation"
                    data-label="Confirm Password"
                    placeholder=""
                    required>

            </div>

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>

                <x-button type="submit" class="ml-4">
                    {{ __('Register') }}
                </x-button>
            </div>
        </form>
    </x-auth-card>
</x-guest-layout>
