<?php

namespace Database\Seeders;

use App\Models\License;
use Illuminate\Database\Seeder;

class LicenseSeeder extends Seeder
{
    public function run(): void
    {
        $licenses = [
            [
                'license_code' => 'LIC00001',
                'name' => 'Microsoft Office 365',
                'vendor' => 'Microsoft',
                'license_key' => 'XXXXX-XXXXX-XXXXX-XXXXX-11111',
                'license_type' => 'per-user',
                'total_quantity' => 50,
                'used_quantity' => 35,
                'purchase_date' => '1/1/2023',
                'expiry_date' => '1/1/2024',
                'cost_per_license' => 12.50,
                'status' => 'active',
            ],
            [
                'license_code' => 'LIC00002',
                'name' => 'Adobe Creative Cloud',
                'vendor' => 'Adobe',
                'license_key' => 'XXXXX-XXXXX-XXXXX-XXXXX-22222',
                'license_type' => 'per-user',
                'total_quantity' => 10,
                'used_quantity' => 8,
                'purchase_date' => '2/1/2023',
                'expiry_date' => '2/1/2024',
                'cost_per_license' => 54.99,
                'status' => 'active',
            ],
            [
                'license_code' => 'LIC00003',
                'name' => 'Slack Pro',
                'vendor' => 'Slack Technologies',
                'license_key' => 'XXXXX-XXXXX-XXXXX-XXXXX-33333',
                'license_type' => 'per-user',
                'total_quantity' => 100,
                'used_quantity' => 75,
                'purchase_date' => '1/15/2023',
                'expiry_date' => '1/15/2024',
                'cost_per_license' => 8.00,
                'status' => 'active',
            ],
            
        ];

        foreach ($licenses as $license) {
            License::create($license);
        }
    }
}
