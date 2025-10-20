<x-guest-layout>
    <!-- Session Status -->
    @if (session('status'))
        <div class="status-message">
            <i class="bi bi-check-circle me-2"></i>{{ session('status') }}
        </div>
    @endif

    <div class="mb-4">
        <h2 class="h4 text-center mb-3">Forgot Password?</h2>
        <div class="text-center mb-4">
            <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                <i class="bi bi-key text-primary" style="font-size: 32px;"></i>
            </div>
        </div>
        <p class="text-muted text-center small">
            No problem! Just let us know your email address and we'll email you a password reset link that will allow you to choose a new one.
        </p>
    </div>

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div class="mb-4">
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
                placeholder="Enter your registered email address"
            />
            @error('email')
                <div class="error-message">
                    <i class="bi bi-exclamation-triangle me-1"></i>{{ $message }}
                </div>
            @enderror
        </div>

        <div class="d-grid gap-2 mb-3">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="bi bi-send me-2"></i>Send Password Reset Link
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

    <div class="mt-4">
        <div class="alert alert-info">
            <h6 class="alert-heading">
                <i class="bi bi-info-circle me-2"></i>What happens next?
            </h6>
            <ul class="mb-0">
                <li>We'll send a secure reset link to your email</li>
                <li>Click the link to create a new password</li>
                <li>The link expires after 60 minutes for security</li>
            </ul>
        </div>
    </div>
</x-guest-layout>
