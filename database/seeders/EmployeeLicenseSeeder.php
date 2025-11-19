<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\License;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeeLicenseSeeder extends Seeder
{
    public function run(): void
    {
        $assignments = [
            // Software Development Team
            [
                'employee_id' => 1, // Ahmed Al-Rahman
                'license_id' => 1, // Microsoft 365 E3
                'assigned_date' => '2024-01-02',
                'expiry_date' => '2025-01-01',
                'status' => 'active',
            ],
            [
                'employee_id' => 1,
                'license_id' => 4, // JetBrains All Products
                'assigned_date' => '2024-03-02',
                'expiry_date' => '2025-03-01',
                'status' => 'active',
            ],
            [
                'employee_id' => 6, // Fatima Zahra
                'license_id' => 1, // Microsoft 365 E3
                'assigned_date' => '2024-01-02',
                'expiry_date' => '2025-01-01',
                'status' => 'active',
            ],
            [
                'employee_id' => 6,
                'license_id' => 4, // JetBrains All Products
                'assigned_date' => '2024-03-02',
                'expiry_date' => '2025-03-01',
                'status' => 'active',
            ],

            // Marketing Team
            [
                'employee_id' => 2, // Sarah Johnson
                'license_id' => 2, // Adobe Creative Cloud
                'assigned_date' => '2024-02-02',
                'expiry_date' => '2025-02-01',
                'status' => 'active',
            ],
            [
                'employee_id' => 2,
                'license_id' => 1, // Microsoft 365 E3
                'assigned_date' => '2024-01-02',
                'expiry_date' => '2025-01-01',
                'status' => 'active',
            ],

            // Finance Team
            [
                'employee_id' => 3, // Mohammed Ali
                'license_id' => 1, // Microsoft 365 E3
                'assigned_date' => '2024-01-02',
                'expiry_date' => '2025-01-01',
                'status' => 'active',
            ],
            [
                'employee_id' => 3,
                'license_id' => 7, // Microsoft Office 2021
                'assigned_date' => '2023-06-25',
                'expiry_date' => '2028-06-20',
                'status' => 'active',
            ],

            // HR Team
            [
                'employee_id' => 4, // Lisa Chen
                'license_id' => 1, // Microsoft 365 E3
                'assigned_date' => '2024-01-02',
                'expiry_date' => '2025-01-01',
                'status' => 'active',
            ],

            // IT Support Team
            [
                'employee_id' => 5, // Omar Abdullah
                'license_id' => 1, // Microsoft 365 E3
                'assigned_date' => '2024-01-02',
                'expiry_date' => '2025-01-01',
                'status' => 'active',
            ],
            [
                'employee_id' => 5,
                'license_id' => 3, // Windows 11 Pro
                'assigned_date' => '2024-01-20',
                'expiry_date' => '2030-01-15',
                'status' => 'active',
            ],

            // Engineering Team (additional assignments)
            [
                'employee_id' => 1,
                'license_id' => 6, // AutoCAD 2024
                'assigned_date' => '2024-02-20',
                'expiry_date' => '2025-02-15',
                'status' => 'active',
            ],

            // Expired license example
            [
                'employee_id' => 2,
                'license_id' => 8, // Adobe Acrobat Pro (expired)
                'assigned_date' => '2023-04-15',
                'expiry_date' => '2026-04-10',
                'status' => 'expired',
            ],
        ];

        foreach ($assignments as $assignment) {
            DB::table('employee_license')->insert($assignment);
        }

        // Update used quantities in licenses table
        $licenseUsage = DB::table('employee_license')
            ->where('status', 'active')
            ->groupBy('license_id')
            ->select('license_id', DB::raw('count(*) as used_count'))
            ->get();

        foreach ($licenseUsage as $usage) {
            License::where('id', $usage->license_id)->update(['used_quantity' => $usage->used_count]);
        }
    }
}
