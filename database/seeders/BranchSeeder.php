<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branches = [
            [
                'name' => 'Main Branch',
                'address' => '123 Main Street, City Center',
                'phone' => '+92-300-1234567',
                'manager_name' => 'John Doe',
                'is_active' => true,
            ],
            [
                'name' => 'North Branch',
                'address' => '456 North Avenue, North City',
                'phone' => '+92-300-9876543',
                'manager_name' => 'Jane Smith',
                'is_active' => true,
            ],
            [
                'name' => 'South Branch',
                'address' => '789 South Road, South District',
                'phone' => '+92-300-5555555',
                'manager_name' => 'Mike Johnson',
                'is_active' => true,
            ]
        ];

        foreach ($branches as $branch) {
            \App\Models\Branch::firstOrCreate(
                ['name' => $branch['name']],
                $branch
            );
        }
    }
}
