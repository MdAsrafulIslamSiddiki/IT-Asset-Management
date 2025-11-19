<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\Employee;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeeAssetSeeder extends Seeder
{
    public function run(): void
    {
        $assignments = [
            // Ahmed Al-Rahman - Senior Developer (Multiple assets)
            [
                'asset_id' => 1, // MacBook Pro 16" M3 Max
                'employee_id' => 1,
                'assigned_date' => '2024-01-20',
                'return_date' => null,
                'assignment_status' => 'active',
                'assignment_notes' => 'Assigned for software development work. High-performance machine required for coding and testing.',
            ],
            [
                'asset_id' => 3, // iPhone 15 Pro Max
                'employee_id' => 1,
                'assigned_date' => '2024-03-15',
                'return_date' => null,
                'assignment_status' => 'active',
                'assignment_notes' => 'Company phone for business communications and on-call support.',
            ],

            // Sarah Johnson - Marketing Director
            [
                'asset_id' => 2, // Dell XPS 15
                'employee_id' => 2,
                'assigned_date' => '2024-02-25',
                'return_date' => null,
                'assignment_status' => 'active',
                'assignment_notes' => 'Assigned for marketing campaigns and creative work.',
            ],

            // Mohammed Ali - Financial Analyst
            [
                'asset_id' => 7, // HP EliteBook 840 G9
                'employee_id' => 3,
                'assigned_date' => '2023-11-20',
                'return_date' => null,
                'assignment_status' => 'active',
                'assignment_notes' => 'Finance department laptop for financial modeling and analysis.',
            ],

            // Lisa Chen - HR Business Partner
            [
                'asset_id' => 6, // iPad Pro 12.9" M2
                'employee_id' => 4,
                'assigned_date' => '2024-04-01',
                'return_date' => null,
                'assignment_status' => 'active',
                'assignment_notes' => 'For HR presentations and employee onboarding sessions.',
            ],

            // Omar Abdullah - IT Support Specialist
            [
                'asset_id' => 5, // Lenovo ThinkPad X1 Carbon
                'employee_id' => 5,
                'assigned_date' => '2024-02-01',
                'return_date' => '2024-05-15',
                'assignment_status' => 'returned',
                'assignment_notes' => 'Laptop sent for maintenance due to keyboard issues. Expected return in 2 weeks.',
            ],

            // Fatima Zahra - Frontend Developer
            [
                'asset_id' => 4, // Samsung Galaxy S24 Ultra
                'employee_id' => 6,
                'assigned_date' => '2024-04-10',
                'return_date' => null,
                'assignment_status' => 'active',
                'assignment_notes' => 'Mobile testing device for frontend development and responsive design testing.',
            ],
        ];

        foreach ($assignments as $assignment) {
            DB::table('employee_asset')->insert($assignment);
        }

        // Update asset status based on assignments
        Asset::whereIn('id', [1, 2, 3, 4, 6, 7])->update(['status' => 'assigned']);
        Asset::where('id', 5)->update(['status' => 'maintenance']);
    }
}
