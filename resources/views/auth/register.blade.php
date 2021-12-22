<x-guest-layout>

    <x-register-card>
        <x-slot name="logo">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </x-slot>

        <div class="text-xl text-white mb-2">Register Account</div>

        <div class="grid grid-cols-1 sm:grid-cols-2">

            <div>

                <form method="POST" action="{{ route('password.update') }}">
                    @csrf

                    <div class="text-white">

                        <!-- Password Reset Token -->
                        <input type="hidden" name="token" value="{{ $_GET['token'] }}">

                        <!-- Name -->
                        <div class="pt-5">
                            <div class="font-semibold">{{ $user -> name }}</div>
                            <input type="hidden" name="name" value="{{ $user -> name }}">

                        </div>

                        <!-- Email Address -->
                        <div class="mt-2">

                            <div class="font-semibold">{{ $user -> email }}</div>
                            <input type="hidden" name="email" value="{{ $user -> email }}">

                        </div>

                        <!-- Password -->
                        <div class="mt-4">

                            <input type="password" class="form-element input md"
                                id="password"
                                name="password"
                                data-label=""
                                placeholder="Password"
                                required autofocus autocomplete="new-password">

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

                        <div class="flex items-center justify-around mt-8">

                            <x-button type="submit" class="ml-4 button default lg">
                                {{ __('Register') }} <i class="fal fa-check ml-2"></i>
                            </x-button>
                        </div>

                    </div>

                </form>

            </div>

            <div class="sm:ml-4 md:ml-8 mt-6 mb-12 sm:mt-0 sm:mb-0 h-full">

                <div class="flex justify-around items-center h-full text-sm text-white">
                    <div>
                        <div class="text-lg mb-2">Password Requirements:</div>

                        <i class="fal fa-arrow-right fa-xs mr-1"></i> One uppercase letter<br>
                        <i class="fal fa-arrow-right fa-xs mr-1"></i> One lower case letter<br>
                        <i class="fal fa-arrow-right fa-xs mr-1"></i> One numeric value<br>
                        {{-- <i class="fal fa-arrow-right fa-xs mr-1"></i> One special character<br> --}}
                        <i class="fal fa-arrow-right fa-xs mr-1"></i> Must be at least 8 characters
                    </div>
                </div>

            </div>

        </div>

        <!-- Validation Errors -->
        <x-auth-validation-errors class="validation-errors" :errors="$errors" />

    </x-register-card>
</x-guest-layout>
