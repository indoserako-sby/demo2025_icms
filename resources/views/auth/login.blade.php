<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="w-100">
        @csrf

        <!-- Email Address -->
        <div class="mb-3">
            <label for="email" class="form-label">{{ __('Email Address') }}</label>
            <input type="email" class="form-control border-1 bg-transparent border-secondary-subtle rounded-2"
                style="border-radius: 8px !important;" id="email" name="email" value="{{ old('email') }}"
                placeholder="name@company.com" required autofocus autocomplete="username" />
            @error('email')
                <div class="text-danger mt-1 small">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="form-password-toggle mb-3">
            <label class="form-label" for="basic-default-password12">Password</label>
            <div class="input-group">
                <input type="password" class="form-control border-1 bg-transparent border-secondary-subtle"
                    style="border-radius: 8px 0 0 8px !important;" id="basic-default-password12" name="password"
                    placeholder="············" aria-describedby="basic-default-password2" required
                    autocomplete="current-password">
                <span id="basic-default-password2"
                    class="input-group-text border-1 bg-transparent border-secondary-subtle cursor-pointer"
                    style="border-radius: 0 8px 8px 0 !important;"><i class="ti ti-eye-off"></i></span>
            </div>
            @error('password')
                <div class="text-danger mt-1 small">{{ $message }}</div>
            @enderror
        </div>



        <div class="mb-3">
            <button type="submit" class="btn w-100 fw-semibold py-2"
                style="font-size: 1rem; background-color: rgba(0, 47, 108, 0.85); color: white;">
                <i class="ti ti-login me-2"></i> {{ __('Sign in') }}
            </button>
        </div>

        <div class="login-divider">
            <span>PT. Indoserako Sejahtera</span>
        </div>

        <div class="text-center small text-muted">
            <p class="mb-0">© {{ date('Y') }} PT Indoserako Sejahtera</p>
        </div>
    </form>
</x-guest-layout>
