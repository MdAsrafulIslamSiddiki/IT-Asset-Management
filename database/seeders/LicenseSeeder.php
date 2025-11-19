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
                'license_code' => 'LIC2024001',
                'name' => 'Microsoft 365 E3',
                'vendor' => 'Microsoft',
                'license_key' => 'M365-XXXXX-XXXXX-XXXXX-11111',
                'license_type' => 'per-user',
                'total_quantity' => 100,
                'used_quantity' => 78,
                'purchase_date' => '2024-01-01',
                'expiry_date' => '2025-01-01',
                'cost_per_license' => 20.00,
                'status' => 'active',
                'notes' => 'Annual subscription for office productivity suite',
            ],
            [
                'license_code' => 'LIC2024002',
                'name' => 'Adobe Creative Cloud All Apps',
                'vendor' => 'Adobe',
                'license_key' => 'ADOBE-XXXXX-XXXXX-XXXXX-22222',
                'license_type' => 'per-user',
                'total_quantity' => 25,
                'used_quantity' => 18,
                'purchase_date' => '2024-02-01',
                'expiry_date' => '2025-02-01',
                'cost_per_license' => 79.99,
                'status' => 'active',
                'notes' => 'Creative software suite for design team',
            ],
            [
                'license_code' => 'LIC2024003',
                'name' => 'Windows 11 Pro',
                'vendor' => 'Microsoft',
                'license_key' => 'WIN11-XXXXX-XXXXX-XXXXX-33333',
                'license_type' => 'per-device',
                'total_quantity' => 150,
                'used_quantity' => 112,
                'purchase_date' => '2024-01-15',
                'expiry_date' => '2030-01-15',
                'cost_per_license' => 199.00,
                'status' => 'active',
                'notes' => 'Operating system licenses for company devices',
            ],
            [
                'license_code' => 'LIC2024004',
                'name' => 'JetBrains All Products Pack',
                'vendor' => 'JetBrains',
                'license_key' => 'JETBR-XXXXX-XXXXX-XXXXX-44444',
                'license_type' => 'per-user',
                'total_quantity' => 15,
                'used_quantity' => 12,
                'purchase_date' => '2024-03-01',
                'expiry_date' => '2025-03-01',
                'cost_per_license' => 49.00,
                'status' => 'active',
                'notes' => 'Development tools for software engineering team',
            ],
            [
                'license_code' => 'LIC2024005',
                'name' => 'Antivirus Enterprise',
                'vendor' => 'Kaspersky',
                'license_key' => 'KASP-XXXXX-XXXXX-XXXXX-55555',
                'license_type' => 'site-license',
                'total_quantity' => 1,
                'used_quantity' => 1,
                'purchase_date' => '2024-01-10',
                'expiry_date' => '2025-01-10',
                'cost_per_license' => 2500.00,
                'status' => 'active',
                'notes' => 'Site-wide antivirus protection for all company devices',
            ],
            [
                'license_code' => 'LIC2024006',
                'name' => 'AutoCAD 2024',
                'vendor' => 'Autodesk',
                'license_key' => 'AUTOD-XXXXX-XXXXX-XXXXX-66666',
                'license_type' => 'per-user',
                'total_quantity' => 10,
                'used_quantity' => 8,
                'purchase_date' => '2024-02-15',
                'expiry_date' => '2025-02-15',
                'cost_per_license' => 220.00,
                'status' => 'active',
                'notes' => 'CAD software for engineering department',
            ],
            [
                'license_code' => 'LIC2023078',
                'name' => 'Microsoft Office 2021',
                'vendor' => 'Microsoft',
                'license_key' => 'OFF21-XXXXX-XXXXX-XXXXX-77777',
                'license_type' => 'per-device',
                'total_quantity' => 50,
                'used_quantity' => 45,
                'purchase_date' => '2023-06-20',
                'expiry_date' => '2028-06-20',
                'cost_per_license' => 149.99,
                'status' => 'active',
                'notes' => 'Perpetual license for office applications',
            ],
            [
                'license_code' => 'LIC2023055',
                'name' => 'Adobe Acrobat Pro',
                'vendor' => 'Adobe',
                'license_key' => 'ACROB-XXXXX-XXXXX-XXXXX-88888',
                'license_type' => 'per-user',
                'total_quantity' => 20,
                'used_quantity' => 15,
                'purchase_date' => '2023-04-10',
                'expiry_date' => '2024-04-10',
                'cost_per_license' => 24.99,
                'status' => 'expired',
                'notes' => 'PDF editing software - needs renewal',
            ],
        ];

        foreach ($licenses as $license) {
            License::create($license);
        }
    }
}
