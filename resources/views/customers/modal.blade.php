<!-- Customer Modal -->
<div class="modal fade" id="customerModal" tabindex="-1" aria-labelledby="customerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="customerModalLabel">Add New Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="customerForm" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    @csrf
                    <input type="hidden" id="customer_id" name="customer_id">
                    <input type="hidden" id="form_method" name="_method" value="">
                    <div class="row g-3">
                        <!-- Customer Information -->
                        <div class="col-12">
                            <h6 class="text-primary border-bottom pb-2 mb-3">Customer Information</h6>
                        </div>
                        
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
                        
                        <div class="col-12">
                            <div class="form-floating">
                                <textarea class="form-control" id="address" name="address" placeholder="Complete Address" style="height: 80px" required></textarea>
                                <label for="address">Complete Address <span class="text-danger">*</span></label>
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
                        
                        <!-- Guarantor Information -->
                        <div class="col-12 mt-4">
                            <h6 class="text-secondary border-bottom pb-2 mb-3">Guarantor Information</h6>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="guarantor_name" name="guarantor_name" placeholder="Guarantor Name">
                                <label for="guarantor_name">Guarantor Name</label>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="guarantor_phone" class="form-label">Guarantor Phone</label>
                            <div class="input-group">
                                <span class="input-group-text">+92</span>
                                <input type="tel" class="form-control" id="guarantor_phone" name="guarantor_phone" placeholder="3001234567">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="guarantor_cnic" class="form-label">Guarantor CNIC</label>
                            <input type="text" class="form-control" id="guarantor_cnic" name="guarantor_cnic" placeholder="12345-6789012-3" pattern="[0-9]{5}-[0-9]{7}-[0-9]{1}">
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="guarantor_relation" name="guarantor_relation" placeholder="Relation">
                                <label for="guarantor_relation">Relation to Customer</label>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <div class="form-floating">
                                <textarea class="form-control" id="guarantor_address" name="guarantor_address" placeholder="Guarantor Address" style="height: 60px"></textarea>
                                <label for="guarantor_address">Guarantor Address</label>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i>Save Customer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>