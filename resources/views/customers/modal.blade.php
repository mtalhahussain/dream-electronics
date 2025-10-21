<!-- Customer Modal -->
<div class="modal fade" id="customerModal" tabindex="-1" aria-labelledby="customerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="customerModalLabel">
                    <i class="bi bi-person-plus me-2"></i>Add New Customer
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="customerForm" method="POST" enctype="multipart/form-data">
                <div class="modal-body p-4" style="max-height: 70vh; overflow-y: auto;">
                    @csrf
                    <input type="hidden" id="customer_id" name="customer_id">
                    <input type="hidden" id="form_method" name="_method" value="">
                    
                    <!-- Progress Steps -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="progress" style="height: 4px;">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: 33%" id="progressBar"></div>
                            </div>
                            <div class="d-flex justify-content-between mt-2">
                                <small class="text-primary fw-bold" id="step1">1. Customer Info</small>
                                <small class="text-muted" id="step2">2. Guarantor 1</small>
                                <small class="text-muted" id="step3">3. Guarantor 2</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Step 1: Customer Information -->
                    <div class="step-content" id="stepContent1">
                        <div class="row g-3">
                            <!-- Customer Information Card -->
                            <div class="col-12">
                                <div class="card border-primary">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="mb-0">
                                            <i class="bi bi-person me-2"></i>Customer Information
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <select class="form-select" id="branch_id" name="branch_id">
                                                        <option value="">Select Branch</option>
                                                        @if(isset($branches))
                                                            @foreach($branches as $branch)
                                                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                    <label for="branch_id">Branch</label>
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="name" name="name" placeholder="Full Name" required>
                                                    <label for="name">Full Name <span class="text-danger">*</span></label>
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input type="email" class="form-control" id="email" name="email" placeholder="Email Address">
                                                    <label for="email">Email Address</label>
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text">+92</span>
                                                    <input type="tel" class="form-control" id="phone" name="phone" placeholder="3001234567" required>
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <label for="cnic" class="form-label">CNIC <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="cnic" name="cnic" placeholder="12345-6789012-3" pattern="[0-9]{5}-[0-9]{7}-[0-9]{1}" required>
                                                <div class="form-text">Format: 12345-6789012-3</div>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="profession" name="profession" placeholder="Profession">
                                                    <label for="profession">Profession</label>
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="father_husband_name" name="father_husband_name" placeholder="Father/Husband Name">
                                                    <label for="father_husband_name">Father/Husband Name</label>
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <select class="form-select" id="is_active" name="is_active">
                                                        <option value="1">Active</option>
                                                        <option value="0">Inactive</option>
                                                    </select>
                                                    <label for="is_active">Status</label>
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-12">
                                                <div class="form-floating">
                                                    <textarea class="form-control" id="address" name="address" placeholder="Complete Address" style="height: 80px" required></textarea>
                                                    <label for="address">Complete Address <span class="text-danger">*</span></label>
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Customer Photos Card -->
                            <div class="col-12">
                                <div class="card border-info">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="mb-0">
                                            <i class="bi bi-camera me-2"></i>Customer Photos (Optional)
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label for="biometric" class="form-label">Biometric Photo</label>
                                                <input type="file" class="form-control" id="biometric" name="biometric" accept="image/*">
                                                <div class="form-text">Upload customer biometric image (JPG, PNG, max 1MB)</div>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <label for="face_photo" class="form-label">Face Photo</label>
                                                <input type="file" class="form-control" id="face_photo" name="face_photo" accept="image/*">
                                                <div class="form-text">Upload customer face photo (JPG, PNG, max 1MB)</div>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Navigation Buttons -->
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-secondary" disabled id="prevBtn">
                                <i class="bi bi-arrow-left me-2"></i>Previous
                            </button>
                            <button type="button" class="btn btn-primary" onclick="nextStep()" id="nextBtn">
                                Next<i class="bi bi-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Step 2: Guarantor 1 Information -->
                    <div class="step-content d-none" id="stepContent2">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="card border-secondary">
                                    <div class="card-header bg-secondary text-white">
                                        <h6 class="mb-0">
                                            <i class="bi bi-person-check me-2"></i>Guarantor 1 Information
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="guarantor_1_name" name="guarantor_1_name" placeholder="Guarantor 1 Name">
                                                    <label for="guarantor_1_name">Guarantor Name</label>
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input type="email" class="form-control" id="guarantor_1_email" name="guarantor_1_email" placeholder="Guarantor 1 Email">
                                                    <label for="guarantor_1_email">Email Address</label>
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <label for="guarantor_1_phone" class="form-label">Phone Number</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">+92</span>
                                                    <input type="tel" class="form-control" id="guarantor_1_phone" name="guarantor_1_phone" placeholder="3001234567">
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <label for="guarantor_1_cnic" class="form-label">CNIC</label>
                                                <input type="text" class="form-control" id="guarantor_1_cnic" name="guarantor_1_cnic" placeholder="12345-6789012-3" pattern="[0-9]{5}-[0-9]{7}-[0-9]{1}">
                                                <div class="invalid-feedback"></div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="guarantor_1_profession" name="guarantor_1_profession" placeholder="Guarantor 1 Profession">
                                                    <label for="guarantor_1_profession">Profession</label>
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="guarantor_1_father_husband_name" name="guarantor_1_father_husband_name" placeholder="Father/Husband Name">
                                                    <label for="guarantor_1_father_husband_name">Father/Husband Name</label>
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="guarantor_1_relation" name="guarantor_1_relation" placeholder="Relation">
                                                    <label for="guarantor_1_relation">Relation to Customer</label>
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <label for="guarantor_1_biometric" class="form-label">Biometric Photo</label>
                                                <input type="file" class="form-control" id="guarantor_1_biometric" name="guarantor_1_biometric" accept="image/*">
                                                <div class="form-text">Upload guarantor biometric image</div>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                            
                                            <div class="col-12">
                                                <div class="form-floating">
                                                    <textarea class="form-control" id="guarantor_1_address" name="guarantor_1_address" placeholder="Guarantor 1 Address" style="height: 80px"></textarea>
                                                    <label for="guarantor_1_address">Complete Address</label>
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Navigation Buttons -->
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-secondary" onclick="prevStep()" id="prevBtn2">
                                <i class="bi bi-arrow-left me-2"></i>Previous
                            </button>
                            <button type="button" class="btn btn-primary" onclick="nextStep()">
                                Next<i class="bi bi-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Step 3: Guarantor 2 Information -->
                    <div class="step-content d-none" id="stepContent3">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="card border-warning">
                                    <div class="card-header bg-warning text-dark">
                                        <h6 class="mb-0">
                                            <i class="bi bi-person-plus me-2"></i>Guarantor 2 Information (Optional)
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="alert alert-info">
                                            <i class="bi bi-info-circle me-2"></i>
                                            Adding a second guarantor is optional but recommended for better security.
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="guarantor_2_name" name="guarantor_2_name" placeholder="Guarantor 2 Name">
                                                    <label for="guarantor_2_name">Guarantor Name</label>
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input type="email" class="form-control" id="guarantor_2_email" name="guarantor_2_email" placeholder="Guarantor 2 Email">
                                                    <label for="guarantor_2_email">Email Address</label>
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <label for="guarantor_2_phone" class="form-label">Phone Number</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">+92</span>
                                                    <input type="tel" class="form-control" id="guarantor_2_phone" name="guarantor_2_phone" placeholder="3001234567">
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <label for="guarantor_2_cnic" class="form-label">CNIC</label>
                                                <input type="text" class="form-control" id="guarantor_2_cnic" name="guarantor_2_cnic" placeholder="12345-6789012-3" pattern="[0-9]{5}-[0-9]{7}-[0-9]{1}">
                                                <div class="invalid-feedback"></div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="guarantor_2_profession" name="guarantor_2_profession" placeholder="Guarantor 2 Profession">
                                                    <label for="guarantor_2_profession">Profession</label>
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="guarantor_2_father_husband_name" name="guarantor_2_father_husband_name" placeholder="Father/Husband Name">
                                                    <label for="guarantor_2_father_husband_name">Father/Husband Name</label>
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="guarantor_2_relation" name="guarantor_2_relation" placeholder="Relation">
                                                    <label for="guarantor_2_relation">Relation to Customer</label>
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <label for="guarantor_2_biometric" class="form-label">Biometric Photo</label>
                                                <input type="file" class="form-control" id="guarantor_2_biometric" name="guarantor_2_biometric" accept="image/*">
                                                <div class="form-text">Upload guarantor biometric image</div>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                            
                                            <div class="col-12">
                                                <div class="form-floating">
                                                    <textarea class="form-control" id="guarantor_2_address" name="guarantor_2_address" placeholder="Guarantor 2 Address" style="height: 80px"></textarea>
                                                    <label for="guarantor_2_address">Complete Address</label>
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Navigation Buttons -->
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-secondary" onclick="prevStep()">
                                <i class="bi bi-arrow-left me-2"></i>Previous
                            </button>
                            <button type="submit" class="btn btn-success" id="submitBtn">
                                <i class="bi bi-check-circle me-2"></i>Save Customer
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <div class="d-flex justify-content-between w-100 align-items-center">
                        <small class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>
                            All fields marked with <span class="text-danger">*</span> are required
                        </small>
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-2"></i>Cancel
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Step navigation functions for modal
    let currentStep = 1;
    const totalSteps = 3;
    
    function updateProgressBar() {
        const progress = (currentStep / totalSteps) * 100;
        document.getElementById('progressBar').style.width = progress + '%';
        
        // Update step indicators
        for (let i = 1; i <= totalSteps; i++) {
            const stepElement = document.getElementById('step' + i);
            if (i <= currentStep) {
                stepElement.classList.remove('text-muted');
                stepElement.classList.add('text-primary', 'fw-bold');
            } else {
                stepElement.classList.remove('text-primary', 'fw-bold');
                stepElement.classList.add('text-muted');
            }
        }
    }
    
    function showStep(step) {
        // Hide all steps
        document.querySelectorAll('.step-content').forEach(function(content) {
            content.classList.add('d-none');
        });
        
        // Show current step
        document.getElementById('stepContent' + step).classList.remove('d-none');
        
        // Update modal title
        const titles = {
            1: '<i class="bi bi-person-plus me-2"></i>Edit Customer - Step 1 of 3',
            2: '<i class="bi bi-person-check me-2"></i>Edit Customer - Step 2 of 3',
            3: '<i class="bi bi-person-plus-fill me-2"></i>Edit Customer - Step 3 of 3'
        };
        document.getElementById('customerModalLabel').innerHTML = titles[step];
        
        updateProgressBar();
    }
    
    function nextStep() {
        if (currentStep < totalSteps) {
            // Validate current step before proceeding
            if (validateCurrentStep()) {
                currentStep++;
                showStep(currentStep);
            }
        }
    }
    
    function prevStep() {
        if (currentStep > 1) {
            currentStep--;
            showStep(currentStep);
        }
    }
    
    function validateCurrentStep() {
        let isValid = true;
        const currentStepElement = document.getElementById('stepContent' + currentStep);
        const requiredFields = currentStepElement.querySelectorAll('[required]');
        
        requiredFields.forEach(function(field) {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        });
        
        if (!isValid) {
            showToast('Please fill in all required fields before proceeding', 'error');
        }
        
        return isValid;
    }
    
    // Reset modal to first step when closed
    document.addEventListener('DOMContentLoaded', function() {
        const customerModal = document.getElementById('customerModal');
        if (customerModal) {
            customerModal.addEventListener('hidden.bs.modal', function() {
                currentStep = 1;
                showStep(currentStep);
            });
        }
    });
</script>