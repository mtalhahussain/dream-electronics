<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center">
            <div class="me-3">
                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="bi bi-person-gear text-white fs-5"></i>
                </div>
            </div>
            <div>
                <h2 class="h4 mb-1">Profile Settings</h2>
                <p class="text-muted mb-0">Manage your account information and security settings</p>
            </div>
        </div>
    </x-slot>

    <div class="container">
        <div class="row">
            <!-- Profile Overview Card -->
            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="position-relative d-inline-block mb-3">
                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 80px; height: 80px;">
                                <i class="bi bi-person-fill text-white" style="font-size: 32px;"></i>
                            </div>
                            <span class="position-absolute bottom-0 end-0 bg-success rounded-circle border border-white" style="width: 20px; height: 20px;"></span>
                        </div>
                        <h5 class="card-title mb-1">{{ Auth::user()->name }}</h5>
                        <p class="text-muted small mb-3">{{ Auth::user()->email }}</p>
                        
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="border-end">
                                    <h6 class="text-primary mb-0">{{ \Carbon\Carbon::parse(Auth::user()->created_at)->diffInDays() }}</h6>
                                    <small class="text-muted">Days</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="border-end">
                                    <h6 class="text-success mb-0">Active</h6>
                                    <small class="text-muted">Status</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <h6 class="text-info mb-0">{{ Auth::user()->email_verified_at ? 'Verified' : 'Pending' }}</h6>
                                <small class="text-muted">Email</small>
                            </div>
                        </div>
                        
                        <hr class="my-3">
                        <small class="text-muted">
                            <i class="bi bi-calendar me-1"></i>
                            Member since {{ \Carbon\Carbon::parse(Auth::user()->created_at)->format('M Y') }}
                        </small>
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="card border-0 shadow-sm mt-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="bi bi-lightning me-2"></i>Quick Actions
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-house me-2"></i>Go to Dashboard
                            </a>
                            <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                                <i class="bi bi-printer me-2"></i>Print Profile
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Profile Forms -->
            <div class="col-lg-8">
                <!-- Profile Information -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="bi bi-person-lines-fill me-2"></i>Profile Information
                        </h5>
                        <small class="text-muted">Update your account's profile information and email address.</small>
                    </div>
                    <div class="card-body">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>

                <!-- Update Password -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="bi bi-shield-lock me-2"></i>Update Password
                        </h5>
                        <small class="text-muted">Ensure your account is using a long, random password to stay secure.</small>
                    </div>
                    <div class="card-body">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>

                <!-- Delete Account -->
                <div class="card border-0 shadow-sm border-danger">
                    <div class="card-header bg-danger bg-opacity-10">
                        <h5 class="mb-0 text-danger">
                            <i class="bi bi-exclamation-triangle me-2"></i>Delete Account
                        </h5>
                        <small class="text-muted">Once your account is deleted, all of its resources and data will be permanently deleted.</small>
                    </div>
                    <div class="card-body">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
