<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\Branch;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $branches = Branch::all();
        
        if ($branches->count() == 0) {
            $this->command->info('No branches found. Please create branches first.');
            return;
        }

        $employees = [
            [
                'name' => 'Ahmad Hassan',
                'cnic' => '42101-1234567-1',
                'phone' => '03001234567',
                'email' => 'ahmad.hassan@dreamelectronics.com',
                'position' => 'Sales Manager',
                'role' => Employee::ROLE_SALES_MANAGER,
                'permissions' => Employee::getDefaultPermissions(Employee::ROLE_SALES_MANAGER),
                'salary' => 45000,
                'hire_date' => '2024-01-15',
                'is_active' => true,
                'branch_id' => $branches->first()->id
            ],
            [
                'name' => 'Fatima Khan',
                'cnic' => '42101-2345678-2',
                'phone' => '03012345678',
                'email' => 'fatima.khan@dreamelectronics.com',
                'position' => 'Product Manager',
                'role' => Employee::ROLE_PRODUCT_MANAGER,
                'permissions' => Employee::getDefaultPermissions(Employee::ROLE_PRODUCT_MANAGER),
                'salary' => 50000,
                'hire_date' => '2024-02-01',
                'is_active' => true,
                'branch_id' => $branches->first()->id
            ],
            [
                'name' => 'Muhammad Ali',
                'cnic' => '42101-3456789-3',
                'phone' => '03023456789',
                'email' => null,
                'position' => 'Sales Associate',
                'role' => Employee::ROLE_EMPLOYEE,
                'permissions' => Employee::getDefaultPermissions(Employee::ROLE_EMPLOYEE),
                'salary' => 25000,
                'hire_date' => '2024-03-10',
                'is_active' => true,
                'branch_id' => $branches->count() > 1 ? $branches->skip(1)->first()->id : $branches->first()->id
            ],
            [
                'name' => 'Ayesha Malik',
                'cnic' => '42101-4567890-4',
                'phone' => '03034567890',
                'email' => 'ayesha.malik@dreamelectronics.com',
                'position' => 'System Administrator',
                'role' => Employee::ROLE_ADMIN,
                'permissions' => Employee::getDefaultPermissions(Employee::ROLE_ADMIN),
                'salary' => 60000,
                'hire_date' => '2024-01-20',
                'is_active' => true,
                'branch_id' => $branches->first()->id
            ]
        ];

        foreach ($employees as $employeeData) {
            Employee::create($employeeData);
        }

        $this->command->info('Employee seeder completed successfully!');
    }
}