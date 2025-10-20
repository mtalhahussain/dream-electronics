<section>
    <div class="alert alert-danger">
        <h6 class="alert-heading">
            <i class="bi bi-exclamation-triangle me-2"></i>Danger Zone
        </h6>
        <p class="mb-3">
            Once your account is deleted, all of its resources and data will be permanently deleted. 
            Before deleting your account, please download any data or information that you wish to retain.
        </p>
        <hr>
        <div class="d-flex align-items-center">
            <i class="bi bi-shield-exclamation me-2 text-danger"></i>
            <span class="me-auto">This action cannot be undone</span>
            <button 
                type="button" 
                class="btn btn-danger" 
                data-bs-toggle="modal" 
                data-bs-target="#deleteAccountModal"
            >
                <i class="bi bi-trash me-2"></i>Delete Account
            </button>
        </div>
    </div>

    <!-- Delete Account Confirmation Modal -->
    <div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteAccountModalLabel">
                        <i class="bi bi-exclamation-triangle me-2"></i>Confirm Account Deletion
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <form method="post" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')
                    
                    <div class="modal-body">
                        <div class="text-center mb-4">
                            <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                <i class="bi bi-trash text-danger" style="font-size: 32px;"></i>
                            </div>
                        </div>
                        
                        <h6 class="text-center mb-3">Are you sure you want to delete your account?</h6>
                        
                        <div class="alert alert-warning">
                            <h6 class="alert-heading">
                                <i class="bi bi-info-circle me-2"></i>What will happen:
                            </h6>
                            <ul class="mb-0">
                                <li>All your personal data will be permanently deleted</li>
                                <li>Your access to Dream Electronics system will be revoked</li>
                                <li>This action cannot be reversed</li>
                                <li>Any associated records will be anonymized</li>
                            </ul>
                        </div>

                        <div class="mb-3">
                            <label for="delete_password" class="form-label">
                                <i class="bi bi-shield-lock me-2"></i>Confirm with your password
                            </label>
                            <div class="input-group">
                                <input 
                                    id="delete_password" 
                                    name="password" 
                                    type="password" 
                                    class="form-control @error('password', 'userDeletion') is-invalid @enderror" 
                                    placeholder="Enter your current password"
                                    required
                                />
                                <button 
                                    class="btn btn-outline-secondary" 
                                    type="button" 
                                    onclick="togglePasswordVisibility('delete_password', 'deletePasswordIcon')"
                                    title="Show/Hide Password"
                                >
                                    <i class="bi bi-eye" id="deletePasswordIcon"></i>
                                </button>
                            </div>
                            @error('password', 'userDeletion')
                                <div class="invalid-feedback d-block">
                                    <i class="bi bi-exclamation-triangle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>
                        
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="confirmDeletion" required>
                            <label class="form-check-label" for="confirmDeletion">
                                I understand that this action cannot be undone
                            </label>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-2"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-danger" id="deleteAccountBtn" disabled>
                            <i class="bi bi-trash me-2"></i>Delete My Account Permanently
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

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
        
        // Enable delete button only when checkbox is checked and password is entered
        document.addEventListener('DOMContentLoaded', function() {
            const confirmCheckbox = document.getElementById('confirmDeletion');
            const passwordInput = document.getElementById('delete_password');
            const deleteBtn = document.getElementById('deleteAccountBtn');
            
            function toggleDeleteButton() {
                deleteBtn.disabled = !(confirmCheckbox.checked && passwordInput.value.length > 0);
            }
            
            confirmCheckbox.addEventListener('change', toggleDeleteButton);
            passwordInput.addEventListener('input', toggleDeleteButton);
        });
        
        // Show modal if there are validation errors
        @if($errors->userDeletion->isNotEmpty())
            document.addEventListener('DOMContentLoaded', function() {
                const modal = new bootstrap.Modal(document.getElementById('deleteAccountModal'));
                modal.show();
            });
        @endif
    </script>
</section>
