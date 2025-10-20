<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'name',
        'cnic',
        'phone',
        'email',
        'position',
        'role',
        'permissions',
        'salary',
        'hire_date',
        'is_active'
    ];

    protected $casts = [
        'salary' => 'decimal:2',
        'hire_date' => 'date',
        'is_active' => 'boolean',
        'permissions' => 'array',
    ];

    // Define role constants
    const ROLE_EMPLOYEE = 'employee';
    const ROLE_PRODUCT_MANAGER = 'product_manager';
    const ROLE_SALES_MANAGER = 'sales_manager';
    const ROLE_ADMIN = 'admin';

    // Define available permissions
    const PERMISSIONS = [
        'manage_products' => 'Manage Products',
        'manage_categories' => 'Manage Categories',
        'manage_customers' => 'Manage Customers',
        'manage_sales' => 'Manage Sales',
        'view_finance' => 'View Finance',
        'manage_expenses' => 'Manage Expenses',
        'manage_stock' => 'Manage Stock Credits',
        'manage_salaries' => 'Manage Salary Payments',
        'manage_employees' => 'Manage Employees',
        'manage_branches' => 'Manage Branches',
        'manage_sms' => 'Manage SMS',
        'manage_settings' => 'Manage Settings',
    ];

    // Get available roles
    public static function getRoles()
    {
        return [
            self::ROLE_EMPLOYEE => 'Employee',
            self::ROLE_PRODUCT_MANAGER => 'Product Manager',
            self::ROLE_SALES_MANAGER => 'Sales Manager',
            self::ROLE_ADMIN => 'Admin',
        ];
    }

    // Get role display name
    public function getRoleDisplayAttribute()
    {
        return self::getRoles()[$this->role] ?? $this->role;
    }

    // Check if employee has permission
    public function hasPermission($permission)
    {
        if ($this->role === self::ROLE_ADMIN) {
            return true; // Admin has all permissions
        }
        
        return in_array($permission, $this->permissions ?? []);
    }

    // Get default permissions for role
    public static function getDefaultPermissions($role)
    {
        switch ($role) {
            case self::ROLE_PRODUCT_MANAGER:
                return ['manage_products', 'manage_categories', 'manage_stock', 'view_finance'];
            case self::ROLE_SALES_MANAGER:
                return ['manage_customers', 'manage_sales', 'view_finance', 'manage_sms'];
            case self::ROLE_ADMIN:
                return array_keys(self::PERMISSIONS);
            default:
                return ['view_finance'];
        }
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function salaryPayments()
    {
        return $this->hasMany(SalaryPayment::class);
    }
}