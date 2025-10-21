<x-guest-layout>
    <div class="mb-4">
        <h2 class="h4 text-center mb-2">Create Account</h2>
        <p class="text-muted text-center small">Join {{ \App\Models\Setting::get('company_name', 'Dream Electronics') }} Management System</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div class="mb-3">
            <label for="name" class="form-label">
                <i class="bi bi-person me-2"></i>Full Name
            </label>
            <input 
                id="name" 
                class="form-control @error('name') is-invalid @enderror" 
                type="text" 
                name="name" 
                value="{{ old('name') }}" 
                required 
                autofocus 
                autocomplete="name"
                placeholder="Enter your full name"
            />
            @error('name')
                <div class="error-message">
                    <i class="bi bi-exclamation-triangle me-1"></i>{{ $message }}
                </div>
            @enderror
        </div>

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
                    autocomplete="new-password"
                    placeholder="Create a strong password"
                />
                <button 
                    class="btn btn-outline-secondary" 
                    type="button" 
                    onclick="togglePassword('password', 'togglePasswordIcon1')"
                    title="Show/Hide Password"
                >
                    <i class="bi bi-eye" id="togglePasswordIcon1"></i>
                </button>
            </div>
            <div class="form-text">
                <small class="text-muted">
                    <i class="bi bi-info-circle me-1"></i>
                    Password must be at least 8 characters long
                </small>
            </div>
            @error('password')
                <div class="error-message">
                    <i class="bi bi-exclamation-triangle me-1"></i>{{ $message }}
                </div>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="mb-4">
            <label for="password_confirmation" class="form-label">
                <i class="bi bi-lock-fill me-2"></i>Confirm Password
            </label>
            <div class="input-group">
                <input 
                    id="password_confirmation" 
                    class="form-control @error('password_confirmation') is-invalid @enderror"
                    type="password"
                    name="password_confirmation" 
                    required 
                    autocomplete="new-password"
                    placeholder="Confirm your password"
                />
                <button 
                    class="btn btn-outline-secondary" 
                    type="button" 
                    onclick="togglePassword('password_confirmation', 'togglePasswordIcon2')"
                    title="Show/Hide Password"
                >
                    <i class="bi bi-eye" id="togglePasswordIcon2"></i>
                </button>
            </div>
            @error('password_confirmation')
                <div class="error-message">
                    <i class="bi bi-exclamation-triangle me-1"></i>{{ $message }}
                </div>
            @enderror
        </div>

        <div class="d-grid gap-2 mb-3">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="bi bi-person-plus me-2"></i>Create Account
            </button>
        </div>

        <hr class="my-4">
        <div class="text-center">
            <p class="text-muted mb-2">Already have an account?</p>
            <a 
                href="{{ route('login') }}" 
                class="btn btn-outline-light"
            >
                <i class="bi bi-box-arrow-in-right me-2"></i>Sign In Instead
            </a>
        </div>
    </form>

    <script>
        function togglePassword(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const toggleIcon = document.getElementById(iconId);
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.className = 'bi bi-eye-slash';
            } else {
                passwordInput.type = 'password';
                toggleIcon.className = 'bi bi-eye';
            }
        }
        
        // Password strength indicator
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthIndicator = document.querySelector('.form-text small');
            
            if (password.length === 0) {
                strengthIndicator.innerHTML = '<i class="bi bi-info-circle me-1"></i>Password must be at least 8 characters long';
                strengthIndicator.className = 'text-muted';
            } else if (password.length < 8) {
                strengthIndicator.innerHTML = '<i class="bi bi-exclamation-triangle me-1"></i>Password too short';
                strengthIndicator.className = 'text-danger';
            } else if (password.length >= 8 && /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/.test(password)) {
                strengthIndicator.innerHTML = '<i class="bi bi-check-circle me-1"></i>Strong password';
                strengthIndicator.className = 'text-success';
            } else {
                strengthIndicator.innerHTML = '<i class="bi bi-dash-circle me-1"></i>Good password';
                strengthIndicator.className = 'text-warning';
            }
        });
    </script>
</x-guest-layout>
