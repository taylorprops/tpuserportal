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

                <x-elements.input
                    :size="'md'"
                    id="name"
                    type="text"
                    name="name"
                    :value="old('name')"
                    data-label="Name"
                    placeholder="name"
                    required readonly />

            </div>

            <!-- Email Address -->
            <div class="mt-4">

                <x-elements.input
                    :size="'md'"
                    id="email"
                    type="email"
                    name="email"
                    :value="old('email')"
                    data-label="Email"
                    placeholder="Email"
                    required readonly />

            </div>

            <!-- Password -->
            <div class="mt-4">

                <x-elements.input
                    :size="'md'"
                    id="password"
                    {{-- class="block mt-1 w-full" --}}
                    type="password"
                    name="password"
                    data-label="Password"
                    placeholder="Password"
                    required autofocus autocomplete="new-password" />

            </div>

            <!-- Confirm Password -->
            <div class="mt-4">

                <x-elements.input
                    :size="'md'"
                    id="password_confirmation"
                    {{-- class="block mt-1 w-full" --}}
                    type="password"
                    name="password_confirmation"
                    data-label="Confirm Password"
                    placeholder="Confirm Password"
                    required />

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
