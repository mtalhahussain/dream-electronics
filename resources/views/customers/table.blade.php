@if($customers->count() > 0)
    <div class="table-responsive">
        <table class="table table-sm table-hover table-striped align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Account #</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>CNIC</th>
                    <th>Profession</th>
                    <th>Branch</th>
                    <th>Status</th>
                    <th>Guarantor</th>
                    <th>Registration</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($customers as $customer)
                <tr>
                    <td>
                        <code class="small">{{ $customer->account_number ?? 'N/A' }}</code>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                {{ strtoupper(substr($customer->name ?? '', 0, 1)) }}
                            </div>
                            <div>
                                <strong>{{ $customer->name ?? 'N/A' }}</strong>
                                @if($customer->email ?? false)
                                    <br><small class="text-muted">{{ $customer->email }}</small>
                                @endif
                                @if($customer->father_husband_name ?? false)
                                    <br><small class="text-muted">S/O: {{ $customer->father_husband_name }}</small>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>
                        <a href="tel:{{ $customer->phone ?? '' }}" class="text-decoration-none">
                            {{ $customer->phone ?? 'N/A' }}
                        </a>
                    </td>
                    <td>
                        <code class="small">{{ $customer->cnic ?? 'N/A' }}</code>
                    </td>
                    <td>
                        {{ $customer->profession ?? 'N/A' }}
                    </td>
                    <td>
                        @if($customer->branch)
                            <span class="badge bg-primary">{{ $customer->branch->name }}</span>
                        @else
                            <span class="text-muted">No branch</span>
                        @endif
                    </td>
                    <td>
                        @if($customer->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-danger">Inactive</span>
                        @endif
                        <br><small class="text-muted" title="{{ $customer->address }}">{{ Str::limit($customer->address ?? 'N/A', 20) }}</small>
                    </td>
                    <td>
                        @if($customer->guarantors->count() > 0)
                            @php $guarantor = $customer->guarantors->first() @endphp
                            <strong>{{ $guarantor->name }}</strong>
                            @if($guarantor->phone)
                                <br><small class="text-muted">{{ $guarantor->phone }}</small>
                            @endif
                            @if($customer->guarantors->count() > 1)
                                <br><small class="text-info">+{{ $customer->guarantors->count() - 1 }} more</small>
                            @endif
                        @else
                            <span class="text-muted">No guarantor</span>
                        @endif
                    </td>
                    <td>
                        <small>{{ \Carbon\Carbon::parse($customer->created_at ?? now())->format('d-M-Y') }}</small>
                    </td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Actions
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" style="z-index: 1060;">
                                <li><a class="dropdown-item" href="{{ route('customers.show', $customer) }}">
                                    <i class="bi bi-eye me-2"></i>View Details
                                </a></li>
                                @can('edit-customers')
                                <li><a class="dropdown-item" href="#" onclick="editCustomer({{ $customer->id }})">
                                    <i class="bi bi-pencil me-2"></i>Edit
                                </a></li>
                                @endcan
                                <li><a class="dropdown-item" href="#" onclick="viewSales({{ $customer->id }})">
                                    <i class="bi bi-cart3 me-2"></i>View Sales
                                </a></li>
                                @can('delete-customers')
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="#" onclick="deleteCustomer({{ $customer->id }})">
                                    <i class="bi bi-trash me-2"></i>Delete
                                </a></li>
                                @endcan
                            </ul>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="text-center py-5">
        <i class="bi bi-people display-4 text-muted"></i>
        <p class="text-muted mt-3">No customers found</p>
        @can('create-customers')
        <button type="button" class="btn btn-primary" onclick="openCreateModal()">
            <i class="bi bi-plus-circle me-2"></i>Add First Customer
        </button>
        @endcan
    </div>
@endif