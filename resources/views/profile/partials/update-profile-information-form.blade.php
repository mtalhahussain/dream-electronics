<section>
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}">
        @csrf
        @method('patch')

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="name" class="form-label">
                    <i class="bi bi-person me-2"></i>Full Name
                </label>
                <input 
                    id="name" 
                    name="name" 
                    type="text" 
                    class="form-control @error('name') is-invalid @enderror" 
                    value="{{ old('name', $user->name) }}" 
                    required 
                    autofocus 
                    autocomplete="name"
                    placeholder="Enter your full name"
                />
                @error('name')
                    <div class="invalid-feedback">
                        <i class="bi bi-exclamation-triangle me-1"></i>{{ $message }}
                    </div>
                @enderror
            </div>
            
            <div class="col-md-6 mb-3">
                <label for="email" class="form-label">
                    <i class="bi bi-envelope me-2"></i>Email Address
                </label>
                <input 
                    id="email" 
                    name="email" 
                    type="email" 
                    class="form-control @error('email') is-invalid @enderror" 
                    value="{{ old('email', $user->email) }}" 
                    required 
                    autocomplete="username"
                    placeholder="Enter your email address"
                />
                @error('email')
                    <div class="invalid-feedback">
                        <i class="bi bi-exclamation-triangle me-1"></i>{{ $message }}
                    </div>
                @enderror
            </div>
        </div>

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div class="alert alert-warning" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <div>
                        <strong>Email Verification Required</strong><br>
                        <small>Your email address is unverified. 
                            <button 
                                form="send-verification" 
                                class="btn btn-link p-0 align-baseline text-decoration-underline"
                                type="submit"
                            >
                                Click here to re-send the verification email.
                            </button>
                        </small>
                    </div>
                </div>
                
                @if (session('status') === 'verification-link-sent')
                    <div class="mt-2">
                        <div class="alert alert-success alert-sm mb-0">
                            <i class="bi bi-check-circle me-1"></i>
                            A new verification link has been sent to your email address.
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <div class="d-flex align-items-center justify-content-between">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-circle me-2"></i>Save Changes
            </button>

            @if (session('status') === 'profile-updated')
                <div class="alert alert-success alert-sm mb-0 fade show" id="success-message">
                    <i class="bi bi-check-circle me-1"></i>Profile updated successfully!
                </div>
                <script>
                    setTimeout(function() {
                        const alert = document.getElementById('success-message');
                        if (alert) {
                            alert.classList.remove('show');
                            setTimeout(() => alert.remove(), 150);
                        }
                    }, 3000);
                </script>
            @endif
        </div>
    </form>
</section>
