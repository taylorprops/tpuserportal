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

            <div class="text-white">

                @csrf

                <div class="text-lg mb-2">Reset Password</div>

                <!-- Password Reset Token -->
                <input type="hidden" name="token" value="{{ $request -> route('token') }}">

                <!-- Email Address -->
                <div class="mt-5">

                    <input type="text" class="form-element input md"
                        id="email"
                        type="email"
                        name="email"
                        value="{{ $request -> email }}"
                        data-label=""
                        placeholder="Email"
                        required
                        readonly>

                </div>

                <!-- Password -->
                <div class="mt-4">

                    <input type="password" class="form-element input md"
                        id="password"
                        name="password"
                        data-label=""
                        placeholder="Password"
                        autofocus
                        required>

                </div>

                <!-- Confirm Password -->
                <div class="mt-4">

                    <input type="password" class="form-element input md"
                        id="password_confirmation"
                        name="password_confirmation"
                        data-label=""
                        placeholder="Confirm Password"
                        required>

                </div>

                <div class="flex items-center justify-end mt-4">
                    <button type="submit" class="button default lg">Reset Password</button>
                </div>

            </div>

        </form>
    </x-auth-card>
</x-guest-layout>
