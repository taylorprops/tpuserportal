<x-guest-layout>
    <x-register-card>
        <x-slot name="logo">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </x-slot>

        <div class="text-xl text-white mb-2">Reset Password</div>

        <div class="grid grid-cols-1 sm:grid-cols-2">

            <div>

                <form method="POST" action="{{ route('password.update') }}">

                    <div class="text-white">

                        @csrf

                        <!-- Password Reset Token -->
                        <input type="hidden" name="token" value="{{ $request -> route('token') }}">

                        <!-- Email Address -->
                        <div class="mt-5">

                            <div class="font-semibold">{{ $request -> email }}</div>
                            <input type="hidden" name="email" value="{{ $request -> email }}">

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

                        <div class="flex items-center justify-around mt-4">
                            <button type="submit" class="button default lg">Reset Password</button>
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
        <x-auth-validation-errors class="mt-4 text-red-600 bg-white p-2 border border-red-500 rounded" :errors="$errors" />

    </x-register-card>
</x-guest-layout>
