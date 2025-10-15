@extends('layouts.admin')

@section('title', 'Add Customer - Dream Electronics')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-user-plus me-2"></i>Add Customer</h1>
    <a href="{{ route('customers.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i>Back to Customers
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('customers.store') }}" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Full Name *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="cnic" class="form-label">CNIC *</label>
                            <input type="text" class="form-control @error('cnic') is-invalid @enderror" 
                                   id="cnic" name="cnic" value="{{ old('cnic') }}" placeholder="1234567890123" maxlength="13" required>
                            @error('cnic')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Phone Number *</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" name="phone" value="{{ old('phone') }}" required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email') }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="address" class="form-label">Address *</label>
                        <textarea class="form-control @error('address') is-invalid @enderror" 
                                  id="address" name="address" rows="3" required>{{ old('address') }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="biometric" class="form-label">Biometric Image</label>
                            <input type="file" class="form-control @error('biometric') is-invalid @enderror" 
                                   id="biometric" name="biometric" accept=".jpg,.jpeg,.png">
                            <div class="form-text">Supported formats: JPG, PNG (Max: 1MB)</div>
                            @error('biometric')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="face_photo" class="form-label">Face Photo</label>
                            <input type="file" class="form-control @error('face_photo') is-invalid @enderror" 
                                   id="face_photo" name="face_photo" accept=".jpg,.jpeg,.png">
                            <div class="form-text">Supported formats: JPG, PNG (Max: 1MB)</div>
                            @error('face_photo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('customers.index') }}" class="btn btn-secondary me-md-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Save Customer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Format CNIC input
    document.getElementById('cnic').addEventListener('input', function(e) {
        this.value = this.value.replace(/\D/g, '');
    });
</script>
@endpush
@endsection