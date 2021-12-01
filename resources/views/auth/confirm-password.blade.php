<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </x-slot>

        <div class="mb-4 text-sm text-gray-600">
            {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
        </div>



        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf

            <!-- Password -->
            <div>

                <input type="password" class="form-element input md"
                    id="password"
                    name="password"
                    data-label="Password"
                    placeholder=""
                    required autocomplete="current-password">

            </div>

            <div class="flex justify-end mt-4">
                <x-button type="submit">
                    {{ __('Confirm') }}
                </x-button>
            </div>
        </form>

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mt-4 text-red-600 bg-white p-2 border border-red-500 rounded" :errors="$errors" />

    </x-auth-card>
</x-guest-layout>
