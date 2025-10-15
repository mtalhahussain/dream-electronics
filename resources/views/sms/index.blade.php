@extends('layouts.admin')

@section('title', 'SMS Management')

@section('content')
<div class="row">
    <!-- Send SMS Section -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-sms"></i> Send SMS
                </h5>
            </div>
            <div class="card-body">
                <form id="smsForm">
                    @csrf
                    <div class="mb-3">
                        <label for="recipient_type" class="form-label">Recipient Type</label>
                        <select class="form-select" id="recipient_type" name="recipient_type" required>
                            <option value="">Select Recipient Type</option>
                            <option value="single">Single Customer</option>
                            <option value="multiple">Multiple Customers</option>
                        </select>
                    </div>

                    <div class="mb-3" id="single_customer_div" style="display: none;">
                        <label for="customer_id" class="form-label">Select Customer</label>
                        <select class="form-select" id="customer_id" name="customer_id">
                            <option value="">Choose a customer...</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->phone }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3" id="multiple_customers_div" style="display: none;">
                        <label class="form-label">Select Customers</label>
                        <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                            @foreach($customers as $customer)
                                <div class="form-check">
                                    <input class="form-check-input customer-checkbox" type="checkbox" 
                                           value="{{ $customer->id }}" id="customer_{{ $customer->id }}">
                                    <label class="form-check-label" for="customer_{{ $customer->id }}">
                                        {{ $customer->name }} ({{ $customer->phone }})
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-2">
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAllCustomers()">Select All</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearAllCustomers()">Clear All</button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="message" class="form-label">Message</label>
                        <textarea class="form-control" id="message" name="message" rows="4" 
                                  maxlength="1600" required placeholder="Type your message here..."></textarea>
                        <div class="form-text">
                            <span id="charCount">0</span>/1600 characters
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary" id="sendBtn">
                        <span class="spinner-border spinner-border-sm d-none" id="sendSpinner"></span>
                        <i class="fas fa-paper-plane"></i> Send SMS
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Pending Reminders Section -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-bell"></i> Pending Reminders
                </h5>
                <span class="badge bg-warning">{{ $pending_reminders->count() }} due</span>
            </div>
            <div class="card-body">
                @if($pending_reminders->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($pending_reminders as $installment)
                            <div class="list-group-item px-0">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">{{ $installment->sale->customer->name }}</h6>
                                        <p class="mb-1 text-muted">{{ $installment->sale->customer->phone }}</p>
                                        <small class="text-muted">
                                            Due: {{ $installment->due_date->format('d M Y') }} 
                                            - PKR {{ number_format($installment->amount) }}
                                        </small>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                            onclick="sendReminder({{ $installment->id }})">
                                        <i class="fas fa-bell"></i> Remind
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-3">
                        {{ $pending_reminders->links() }}
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <h6 class="text-muted">No pending reminders</h6>
                        <p class="text-muted">All installments are up to date!</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-history"></i> Recent SMS Activities
                </h5>
                <a href="{{ route('sms.logs') }}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-list"></i> View All Logs
                </a>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <i class="fas fa-paper-plane fa-2x mb-2"></i>
                                <h5>Today</h5>
                                <p class="mb-0">0 SMS sent</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <i class="fas fa-check-circle fa-2x mb-2"></i>
                                <h5>This Week</h5>
                                <p class="mb-0">0 SMS sent</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <i class="fas fa-bell fa-2x mb-2"></i>
                                <h5>Reminders</h5>
                                <p class="mb-0">{{ $pending_reminders->count() }} pending</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body text-center">
                                <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                                <h5>Failed</h5>
                                <p class="mb-0">0 failed today</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Handle recipient type change
document.getElementById('recipient_type').addEventListener('change', function() {
    const type = this.value;
    const singleDiv = document.getElementById('single_customer_div');
    const multipleDiv = document.getElementById('multiple_customers_div');
    
    if (type === 'single') {
        singleDiv.style.display = 'block';
        multipleDiv.style.display = 'none';
        document.getElementById('customer_id').required = true;
    } else if (type === 'multiple') {
        singleDiv.style.display = 'none';
        multipleDiv.style.display = 'block';
        document.getElementById('customer_id').required = false;
    } else {
        singleDiv.style.display = 'none';
        multipleDiv.style.display = 'none';
        document.getElementById('customer_id').required = false;
    }
});

// Character count
document.getElementById('message').addEventListener('input', function() {
    document.getElementById('charCount').textContent = this.value.length;
});

// Select/Clear all customers
function selectAllCustomers() {
    document.querySelectorAll('.customer-checkbox').forEach(checkbox => {
        checkbox.checked = true;
    });
}

function clearAllCustomers() {
    document.querySelectorAll('.customer-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
}

// Send SMS form
document.getElementById('smsForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const sendBtn = document.getElementById('sendBtn');
    const spinner = document.getElementById('sendSpinner');
    const recipientType = document.getElementById('recipient_type').value;
    
    if (!recipientType) {
        showAlert('warning', 'Please select recipient type');
        return;
    }
    
    let url, data = new FormData(this);
    
    if (recipientType === 'single') {
        const customerId = document.getElementById('customer_id').value;
        if (!customerId) {
            showAlert('warning', 'Please select a customer');
            return;
        }
        url = '{{ route("sms.send") }}';
    } else {
        const selectedCustomers = Array.from(document.querySelectorAll('.customer-checkbox:checked')).map(cb => cb.value);
        if (selectedCustomers.length === 0) {
            showAlert('warning', 'Please select at least one customer');
            return;
        }
        url = '{{ route("sms.send-bulk") }}';
        selectedCustomers.forEach(id => data.append('customer_ids[]', id));
    }
    
    sendBtn.disabled = true;
    spinner.classList.remove('d-none');
    
    fetch(url, {
        method: 'POST',
        body: data,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
            document.getElementById('smsForm').reset();
            document.getElementById('recipient_type').dispatchEvent(new Event('change'));
        } else {
            showAlert('danger', data.message || 'Failed to send SMS');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('danger', 'An unexpected error occurred');
    })
    .finally(() => {
        sendBtn.disabled = false;
        spinner.classList.add('d-none');
    });
});

// Send reminder
function sendReminder(installmentId) {
    fetch('{{ route("sms.send-reminder") }}', {
        method: 'POST',
        body: JSON.stringify({
            installment_id: installmentId
        }),
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
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showAlert('danger', data.message || 'Failed to send reminder');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('danger', 'An unexpected error occurred');
    });
}

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