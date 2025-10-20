<section>
    <form method="post" action="{{ route('password.update') }}">
        @csrf
        @method('put')

        <div class="mb-3">
            <label for="update_password_current_password" class="form-label">
                <i class="bi bi-shield-lock me-2"></i>Current Password
            </label>
            <div class="input-group">
                <input 
                    id="update_password_current_password" 
                    name="current_password" 
                    type="password" 
                    class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" 
                    autocomplete="current-password"
                    placeholder="Enter your current password"
                />
                <button 
                    class="btn btn-outline-secondary" 
                    type="button" 
                    onclick="togglePasswordVisibility('update_password_current_password', 'currentPasswordIcon')"
                    title="Show/Hide Password"
                >
                    <i class="bi bi-eye" id="currentPasswordIcon"></i>
                </button>
            </div>
            @error('current_password', 'updatePassword')
                <div class="invalid-feedback d-block">
                    <i class="bi bi-exclamation-triangle me-1"></i>{{ $message }}
                </div>
            @enderror
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="update_password_password" class="form-label">
                    <i class="bi bi-key me-2"></i>New Password
                </label>
                <div class="input-group">
                    <input 
                        id="update_password_password" 
                        name="password" 
                        type="password" 
                        class="form-control @error('password', 'updatePassword') is-invalid @enderror" 
                        autocomplete="new-password"
                        placeholder="Enter new password"
                    />
                    <button 
                        class="btn btn-outline-secondary" 
                        type="button" 
                        onclick="togglePasswordVisibility('update_password_password', 'newPasswordIcon')"
                        title="Show/Hide Password"
                    >
                        <i class="bi bi-eye" id="newPasswordIcon"></i>
                    </button>
                </div>
                <div class="form-text">
                    <small id="passwordStrength" class="text-muted">
                        <i class="bi bi-info-circle me-1"></i>
                        Password must be at least 8 characters long
                    </small>
                </div>
                @error('password', 'updatePassword')
                    <div class="invalid-feedback d-block">
                        <i class="bi bi-exclamation-triangle me-1"></i>{{ $message }}
                    </div>
                @enderror
            </div>
            
            <div class="col-md-6 mb-3">
                <label for="update_password_password_confirmation" class="form-label">
                    <i class="bi bi-key-fill me-2"></i>Confirm New Password
                </label>
                <div class="input-group">
                    <input 
                        id="update_password_password_confirmation" 
                        name="password_confirmation" 
                        type="password" 
                        class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror" 
                        autocomplete="new-password"
                        placeholder="Confirm new password"
                    />
                    <button 
                        class="btn btn-outline-secondary" 
                        type="button" 
                        onclick="togglePasswordVisibility('update_password_password_confirmation', 'confirmPasswordIcon')"
                        title="Show/Hide Password"
                    >
                        <i class="bi bi-eye" id="confirmPasswordIcon"></i>
                    </button>
                </div>
                @error('password_confirmation', 'updatePassword')
                    <div class="invalid-feedback d-block">
                        <i class="bi bi-exclamation-triangle me-1"></i>{{ $message }}
                    </div>
                @enderror
            </div>
        </div>

        <div class="alert alert-info">
            <i class="bi bi-lightbulb me-2"></i>
            <strong>Password Security Tips:</strong>
            <ul class="mb-0 mt-2">
                <li>Use at least 8 characters</li>
                <li>Include uppercase and lowercase letters</li>
                <li>Add numbers and special characters</li>
                <li>Avoid common words or personal information</li>
            </ul>
        </div>

        <div class="d-flex align-items-center justify-content-between">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-shield-check me-2"></i>Update Password
            </button>

            @if (session('status') === 'password-updated')
                <div class="alert alert-success alert-sm mb-0 fade show" id="password-success-message">
                    <i class="bi bi-check-circle me-1"></i>Password updated successfully!
                </div>
                <script>
                    setTimeout(function() {
                        const alert = document.getElementById('password-success-message');
                        if (alert) {
                            alert.classList.remove('show');
                            setTimeout(() => alert.remove(), 150);
                        }
                    }, 3000);
                </script>
            @endif
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
        document.getElementById('update_password_password').addEventListener('input', function() {
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
    </script>
</section>
