<?php
namespace App\Services;

use App\Models\License;
use Illuminate\Support\Facades\DB;

class LicenseService
{
    /**
     * Get all licenses with optional filters
     */
    public function getAllLicenses($filters = [])
    {
        $query = License::withCount('employees');

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('vendor', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('license_key', 'like', '%' . $filters['search'] . '%');
            });
        }

        return $query->latest()->get();
    }

    /**
     * Create a new license
     */
    public function createLicense(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Generate unique license code
            $data['license_code'] = $this->generateLicenseCode();
            $data['used_quantity'] = 0;
            $data['status'] = 'active';

            // Use formatted dates if available
            if (isset($data['purchase_date_formatted'])) {
                $data['purchase_date'] = $data['purchase_date_formatted'];
                unset($data['purchase_date_formatted']);
            }

            if (isset($data['expiry_date_formatted'])) {
                $data['expiry_date'] = $data['expiry_date_formatted'];
                unset($data['expiry_date_formatted']);
            }

            return License::create($data);
        });
    }

    /**
     * Update an existing license
     */
    public function updateLicense(License $license, array $data)
    {
        return DB::transaction(function () use ($license, $data) {
            // Prevent reducing total_quantity below used_quantity
            if (isset($data['total_quantity']) && $data['total_quantity'] < $license->used_quantity) {
                throw new \Exception('Total quantity cannot be less than currently assigned licenses (' . $license->used_quantity . ')');
            }

            // Use formatted dates if available
            if (isset($data['purchase_date_formatted'])) {
                $data['purchase_date'] = $data['purchase_date_formatted'];
                unset($data['purchase_date_formatted']);
            }

            if (isset($data['expiry_date_formatted'])) {
                $data['expiry_date'] = $data['expiry_date_formatted'];
                unset($data['expiry_date_formatted']);
            }

            $license->update($data);
            return $license->fresh();
        });
    }

    /**
     * Delete a license
     */
    public function deleteLicense(License $license)
    {
        return DB::transaction(function () use ($license) {
            if ($license->used_quantity > 0) {
                throw new \Exception('Cannot delete license with active assignments. Please revoke all assignments first.');
            }

            return $license->delete();
        });
    }

    /**
     * Assign license to an employee
     */
    public function assignLicense(License $license, $employeeId, $expiryDate = null)
    {
        return DB::transaction(function () use ($license, $employeeId, $expiryDate) {
            // Check available quantity
            if ($license->available_quantity <= 0) {
                throw new \Exception('No available licenses. All ' . $license->total_quantity . ' seats are in use.');
            }

            // Check if already assigned
            if ($license->employees()->where('employee_id', $employeeId)->exists()) {
                throw new \Exception('This license is already assigned to this employee');
            }

            // Assign license
            $license->employees()->attach($employeeId, [
                'assigned_date' => date('n/j/Y'),
                'expiry_date' => $expiryDate,
                'status' => 'active',
            ]);

            // Increment used quantity
            $license->increment('used_quantity');

            return $license->fresh();
        });
    }

    /**
     * Revoke license from an employee
     */
    public function revokeLicense(License $license, $employeeId)
    {
        return DB::transaction(function () use ($license, $employeeId) {
            // Check if assigned
            if (!$license->employees()->where('employee_id', $employeeId)->exists()) {
                throw new \Exception('This license is not assigned to this employee');
            }

            // Detach license
            $license->employees()->detach($employeeId);

            // Decrement used quantity
            $license->decrement('used_quantity');

            return $license->fresh();
        });
    }

    /**
     * Generate unique license code
     */
    private function generateLicenseCode()
    {
        $lastLicense = License::latest('id')->first();
        $number = $lastLicense ? intval(substr($lastLicense->license_code, 3)) + 1 : 1;
        return 'LIC' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Check and update expired licenses
     */
    public function checkExpiredLicenses()
    {
        $today = time();

        License::where('status', 'active')
            ->get()
            ->each(function($license) use ($today) {
                if (strtotime($license->expiry_date) < $today) {
                    $license->update(['status' => 'expired']);
                }
            });
    }
}
