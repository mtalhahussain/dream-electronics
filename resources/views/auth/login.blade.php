<x-guest-layout>
    <!-- Session Status -->
    @if (session('status'))
        <div class="status-message">
            <i class="bi bi-check-circle me-2"></i>{{ session('status') }}
        </div>
    @endif

    <div class="mb-4">
        <h2 class="h4 text-center mb-2">Welcome Back</h2>
        <p class="text-muted text-center small">Sign in to access your account</p>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div class="mb-3">
            <label for="email" class="form-label">
                <i class="bi bi-envelope me-2"></i>Email Address
            </label>
            <input 
                id="email" 
                class="form-control @error('email') is-invalid @enderror" 
                type="email" 
                name="email" 
                value="{{ old('email') }}" 
                required 
                autofocus 
                autocomplete="username"
                placeholder="Enter your email address"
            />
            @error('email')
                <div class="error-message">
                    <i class="bi bi-exclamation-triangle me-1"></i>{{ $message }}
                </div>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label for="password" class="form-label">
                <i class="bi bi-lock me-2"></i>Password
            </label>
            <div class="input-group">
                <input 
                    id="password" 
                    class="form-control @error('password') is-invalid @enderror"
                    type="password"
                    name="password"
                    required 
                    autocomplete="current-password"
                    placeholder="Enter your password"
                />
                <button 
                    class="btn btn-outline-secondary" 
                    type="button" 
                    onclick="togglePassword()"
                    id="togglePasswordBtn"
                    title="Show/Hide Password"
                >
                    <i class="bi bi-eye" id="togglePasswordIcon"></i>
                </button>
            </div>
            @error('password')
                <div class="error-message">
                    <i class="bi bi-exclamation-triangle me-1"></i>{{ $message }}
                </div>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="mb-4">
            <div class="form-check">
                <input 
                    id="remember_me" 
                    type="checkbox" 
                    class="form-check-input" 
                    name="remember"
                    {{ old('remember') ? 'checked' : '' }}
                >
                <label class="form-check-label" for="remember_me">
                    Remember me on this device
                </label>
            </div>
        </div>

        <div class="d-grid gap-2 mb-3">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
            </button>
        </div>

        <div class="text-center">
            @if (Route::has('password.request'))
                <a 
                    href="{{ route('password.request') }}" 
                    class="text-decoration-none text-muted"
                >
                    <i class="bi bi-question-circle me-1"></i>Forgot your password?
                </a>
            @endif
        </div>

        @if (Route::has('register'))
            <hr class="my-4">
            <div class="text-center">
                <p class="text-muted mb-2">Don't have an account?</p>
                <a 
                    href="{{ route('register') }}" 
                    class="btn btn-outline-light"
                >
                    <i class="bi bi-person-plus me-2"></i>Create New Account
                </a>
            </div>
        @endif
    </form>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('togglePasswordIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.className = 'bi bi-eye-slash';
            } else {
                passwordInput.type = 'password';
                toggleIcon.className = 'bi bi-eye';
            }
        }
    </script>
</x-guest-layout>
