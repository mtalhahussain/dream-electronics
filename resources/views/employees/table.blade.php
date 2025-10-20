@forelse($employees as $employee)
<tr>
    <td>
        <strong>{{ $employee->name }}</strong>
        @if($employee->email)
            <br><small class="text-muted">{{ $employee->email }}</small>
        @endif
    </td>
    <td>{{ $employee->position }}</td>
    <td>
        @php
            $roleColors = [
                'admin' => 'danger',
                'sales_manager' => 'warning',
                'product_manager' => 'info',
                'employee' => 'secondary'
            ];
            $color = $roleColors[$employee->role] ?? 'secondary';
        @endphp
        <span class="badge bg-{{ $color }}">
            {{ ucfirst(str_replace('_', ' ', $employee->role)) }}
        </span>
    </td>
    <td>{{ $employee->branch->name ?? 'N/A' }}</td>
    <td>{{ $employee->phone }}</td>
    <td>Rs. {{ number_format((float)$employee->salary, 0) }}</td>
    <td>{{ \Carbon\Carbon::parse($employee->hire_date)->format('M d, Y') }}</td>
    <td>
        @if($employee->is_active)
            <span class="badge bg-success">Active</span>
        @else
            <span class="badge bg-danger">Inactive</span>
        @endif
    </td>
    <td>
        <div class="btn-group btn-group-sm">
            <a href="{{ route('employees.show', $employee) }}" 
               class="btn btn-outline-info me-2" title="View">
                <i class="bi bi-eye"></i>
            </a>
            <a href="{{ route('employees.edit', $employee) }}" 
               class="btn btn-outline-primary me-2" title="Edit">
                <i class="bi bi-pencil"></i>
            </a>
            <button class="btn btn-outline-danger" 
                    onclick="deleteEmployee({{ $employee->id }})" 
                    title="Delete">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="9" class="text-center">
        <div class="py-4">
            <i class="bi bi-person-x fs-1 text-muted"></i>
            <p class="text-muted mt-2">No employees found</p>
        </div>
    </td>
</tr>
@endforelse