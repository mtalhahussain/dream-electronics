<x-guest-layout>
    <div class="mb-4">
        <h2 class="h4 text-center mb-3">Reset Your Password</h2>
        <div class="text-center mb-4">
            <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                <i class="bi bi-shield-check text-success" style="font-size: 32px;"></i>
            </div>
        </div>
        <p class="text-muted text-center small">
            Create a new secure password for your {{ \App\Models\Setting::get('company_name', 'Dream Electronics') }} account.
        </p>
    </div>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

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
                value="{{ old('email', $request->email) }}" 
                required 
                autofocus 
                autocomplete="username"
                placeholder="Your email address"
                readonly
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
                <i class="bi bi-key me-2"></i>New Password
            </label>
            <div class="input-group">
                <input 
                    id="password" 
                    class="form-control @error('password') is-invalid @enderror" 
                    type="password" 
                    name="password" 
                    required 
                    autocomplete="new-password"
                    placeholder="Enter your new password"
                />
                <button 
                    class="btn btn-outline-secondary" 
                    type="button" 
                    onclick="togglePasswordVisibility('password', 'passwordIcon')"
                    title="Show/Hide Password"
                >
                    <i class="bi bi-eye" id="passwordIcon"></i>
                </button>
            </div>
            <div class="form-text">
                <small id="passwordStrength" class="text-muted">
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
                <i class="bi bi-key-fill me-2"></i>Confirm New Password
            </label>
            <div class="input-group">
                <input 
                    id="password_confirmation" 
                    class="form-control @error('password_confirmation') is-invalid @enderror"
                    type="password"
                    name="password_confirmation" 
                    required 
                    autocomplete="new-password"
                    placeholder="Confirm your new password"
                />
                <button 
                    class="btn btn-outline-secondary" 
                    type="button" 
                    onclick="togglePasswordVisibility('password_confirmation', 'confirmPasswordIcon')"
                    title="Show/Hide Password"
                >
                    <i class="bi bi-eye" id="confirmPasswordIcon"></i>
                </button>
            </div>
            @error('password_confirmation')
                <div class="error-message">
                    <i class="bi bi-exclamation-triangle me-1"></i>{{ $message }}
                </div>
            @enderror
        </div>

        <div class="alert alert-info mb-4">
            <i class="bi bi-lightbulb me-2"></i>
            <strong>Password Requirements:</strong>
            <ul class="mb-0 mt-2">
                <li>At least 8 characters long</li>
                <li>Include uppercase and lowercase letters</li>
                <li>Add numbers and special characters</li>
                <li>Avoid common words or personal information</li>
            </ul>
        </div>

        <div class="d-grid gap-2 mb-3">
            <button type="submit" class="btn btn-success btn-lg">
                <i class="bi bi-shield-check me-2"></i>Reset Password
            </button>
        </div>

        <div class="text-center">
            <a 
                href="{{ route('login') }}" 
                class="text-decoration-none text-muted"
            >
                <i class="bi bi-arrow-left me-1"></i>Back to Sign In
            </a>
        </div>
    </form>

    <script>
        function togglePasswordVisibility(inputId, iconId) {
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
            const strengthIndicator = document.getElementById('passwordStrength');
            
            if (password.length === 0) {
                strengthIndicator.innerHTML = '<i class="bi bi-info-circle me-1"></i>Password must be at least 8 characters long';
                strengthIndicator.className = 'text-muted';
            } else if (password.length < 8) {
                strengthIndicator.innerHTML = '<i class="bi bi-exclamation-triangle me-1"></i>Password too short';
                strengthIndicator.className = 'text-danger';
            } else if (password.length >= 8 && /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])/.test(password)) {
                strengthIndicator.innerHTML = '<i class="bi bi-check-circle me-1"></i>Very strong password';
                strengthIndicator.className = 'text-success';
            } else if (password.length >= 8 && /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/.test(password)) {
                strengthIndicator.innerHTML = '<i class="bi bi-check-circle me-1"></i>Strong password';
                strengthIndicator.className = 'text-success';
            } else {
                strengthIndicator.innerHTML = '<i class="bi bi-dash-circle me-1"></i>Good password';
                strengthIndicator.className = 'text-warning';
            }
        });
        
        // Match password confirmation
        document.getElementById('password_confirmation').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (confirmPassword.length > 0) {
                if (password === confirmPassword) {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                } else {
                    this.classList.remove('is-valid');
                    this.classList.add('is-invalid');
                }
            } else {
                this.classList.remove('is-valid', 'is-invalid');
            }
        });
    </script>
</x-guest-layout>
