@extends('layouts.admin')

@section('title', 'Settings')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-cog"></i> Application Settings
                </h5>
            </div>
            <div class="card-body">
                <form id="settingsForm">
                    @csrf
                    
                    <!-- Company Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-building"></i> Company Information
                            </h6>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="company_name" class="form-label">Company Name</label>
                                <input type="text" class="form-control" id="company_name" name="company_name" 
                                       value="{{ $settings['company_name'] }}" required>
                                <div class="invalid-feedback" id="company_nameError"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="company_email" class="form-label">Company Email</label>
                                <input type="email" class="form-control" id="company_email" name="company_email" 
                                       value="{{ $settings['company_email'] }}">
                                <div class="invalid-feedback" id="company_emailError"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="company_phone" class="form-label">Company Phone</label>
                                <input type="text" class="form-control" id="company_phone" name="company_phone" 
                                       value="{{ $settings['company_phone'] }}">
                                <div class="invalid-feedback" id="company_phoneError"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="company_address" class="form-label">Company Address</label>
                                <textarea class="form-control" id="company_address" name="company_address" 
                                          rows="3">{{ $settings['company_address'] }}</textarea>
                                <div class="invalid-feedback" id="company_addressError"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Twilio SMS Configuration -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-sms"></i> Twilio SMS Configuration
                                <button type="button" class="btn btn-sm btn-outline-info ms-2" onclick="testTwilio()">
                                    <i class="fas fa-test-tube"></i> Test Connection
                                </button>
                            </h6>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="twilio_sid" class="form-label">Twilio Account SID</label>
                                <input type="text" class="form-control" id="twilio_sid" name="twilio_sid" 
                                       value="{{ $settings['twilio_sid'] }}" placeholder="ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx">
                                <div class="invalid-feedback" id="twilio_sidError"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="twilio_token" class="form-label">Twilio Auth Token</label>
                                <input type="password" class="form-control" id="twilio_token" name="twilio_token" 
                                       value="{{ $settings['twilio_token'] }}" placeholder="Enter auth token">
                                <div class="invalid-feedback" id="twilio_tokenError"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="twilio_from" class="form-label">Twilio Phone Number</label>
                                <input type="text" class="form-control" id="twilio_from" name="twilio_from" 
                                       value="{{ $settings['twilio_from'] }}" placeholder="+1234567890">
                                <div class="invalid-feedback" id="twilio_fromError"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Business Configuration -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-business-time"></i> Business Configuration
                            </h6>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="default_installment_months" class="form-label">Default Installment Months</label>
                                <input type="number" class="form-control" id="default_installment_months" 
                                       name="default_installment_months" value="{{ $settings['default_installment_months'] }}" 
                                       min="1" max="60" required>
                                <div class="invalid-feedback" id="default_installment_monthsError"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="late_fee_percentage" class="form-label">Late Fee Percentage (%)</label>
                                <input type="number" class="form-control" id="late_fee_percentage" 
                                       name="late_fee_percentage" value="{{ $settings['late_fee_percentage'] }}" 
                                       min="0" max="100" step="0.01" required>
                                <div class="invalid-feedback" id="late_fee_percentageError"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="currency_symbol" class="form-label">Currency Symbol</label>
                                <input type="text" class="form-control" id="currency_symbol" name="currency_symbol" 
                                       value="{{ $settings['currency_symbol'] }}" required>
                                <div class="invalid-feedback" id="currency_symbolError"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Save Button -->
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary" id="saveBtn">
                                <span class="spinner-border spinner-border-sm d-none" id="saveSpinner"></span>
                                <i class="fas fa-save"></i> Save Settings
                            </button>
                            <button type="button" class="btn btn-secondary ms-2" onclick="resetForm()">
                                <i class="fas fa-undo"></i> Reset
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Settings form submission
document.getElementById('settingsForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const saveBtn = document.getElementById('saveBtn');
    const spinner = document.getElementById('saveSpinner');
    
    saveBtn.disabled = true;
    spinner.classList.remove('d-none');
    
    clearValidationErrors();
    
    const formData = new FormData(this);
    
    fetch('{{ route("settings.update") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
        } else {
            if (data.errors) {
                showValidationErrors(data.errors);
            } else {
                showAlert('danger', data.message || 'An error occurred');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('danger', 'An unexpected error occurred');
    })
    .finally(() => {
        saveBtn.disabled = false;
        spinner.classList.add('d-none');
    });
});

// Test Twilio connection
function testTwilio() {
    const testBtn = event.target;
    const originalText = testBtn.innerHTML;
    
    testBtn.disabled = true;
    testBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Testing...';
    
    fetch('{{ route("settings.test-twilio") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
        } else {
            showAlert('danger', data.message || 'Twilio test failed');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('danger', 'An unexpected error occurred during test');
    })
    .finally(() => {
        testBtn.disabled = false;
        testBtn.innerHTML = originalText;
    });
}

// Reset form
function resetForm() {
    if (confirm('Are you sure you want to reset all changes?')) {
        document.getElementById('settingsForm').reset();
        clearValidationErrors();
    }
}

// Clear validation errors
function clearValidationErrors() {
    const inputs = document.querySelectorAll('.form-control');
    inputs.forEach(input => {
        input.classList.remove('is-invalid');
    });
    
    const errorDivs = document.querySelectorAll('.invalid-feedback');
    errorDivs.forEach(div => {
        div.textContent = '';
    });
}

// Show validation errors
function showValidationErrors(errors) {
    for (const [field, messages] of Object.entries(errors)) {
        const input = document.getElementById(field);
        const errorDiv = document.getElementById(field + 'Error');
        
        if (input && errorDiv) {
            input.classList.add('is-invalid');
            errorDiv.textContent = messages[0];
        }
    }
}

// Show alert
function showAlert(type, message) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    const container = document.querySelector('.container-fluid');
    container.insertAdjacentHTML('afterbegin', alertHtml);
    
    // Auto dismiss after 5 seconds
    setTimeout(() => {
        const alert = container.querySelector('.alert');
        if (alert) {
            alert.remove();
        }
    }, 5000);
}
</script>
@endpush