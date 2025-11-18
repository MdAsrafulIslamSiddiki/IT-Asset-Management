<?php
namespace App\Http\Controllers;

use App\Models\License;
use App\Models\Employee;
use App\Http\Requests\StoreLicenseRequest;
use App\Http\Requests\UpdateLicenseRequest;
use App\Services\LicenseService;
use Illuminate\Http\Request;

class LicenseController extends Controller
{
    protected $licenseService;

    public function __construct(LicenseService $licenseService)
    {
        $this->licenseService = $licenseService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['status', 'search']);
        $licenses = $this->licenseService->getAllLicenses($filters);
        $employees = Employee::active()->get();

        return view('licenses.index', compact('licenses', 'employees'));
    }

    public function store(StoreLicenseRequest $request)
    {
        try {
            $license = $this->licenseService->createLicense($request->validated());

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'License created successfully',
                    'license' => $license
                ]);
            }

            return redirect()->route('licenses.index')->with('success', 'License created successfully');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create license'
                ], 500);
            }
            return back()->with('error', 'Failed to create license');
        }
    }

    public function show(License $license)
    {
        $license->load('employees');

        return response()->json([
            'id' => $license->id,
            'license_code' => $license->license_code,
            'name' => $license->name,
            'vendor' => $license->vendor,
            'license_key' => $license->license_key,
            'license_type' => $license->license_type,
            'total_quantity' => $license->total_quantity,
            'used_quantity' => $license->used_quantity,
            'available_quantity' => $license->available_quantity,
            'purchase_date' => $license->purchase_date,
            'expiry_date' => $license->expiry_date,
            'cost_per_license' => $license->cost_per_license,
            'total_cost' => $license->total_cost,
            'status' => $license->status,
            'notes' => $license->notes,
            'employees' => $license->employees->map(fn($e) => [
                'id' => $e->id,
                'name' => $e->name,
                'department' => $e->department,
                'assigned_date' => $e->pivot->assigned_date,
                'expiry_date' => $e->pivot->expiry_date,
                'status' => $e->pivot->status,
            ])
        ]);
    }

    public function edit(License $license)
    {
        return response()->json([
            'name' => $license->name,
            'vendor' => $license->vendor,
            'license_key' => $license->license_key,
            'license_type' => $license->license_type,
            'total_quantity' => $license->total_quantity,
            'purchase_date' => $license->purchase_date,
            'expiry_date' => $license->expiry_date,
            'cost_per_license' => $license->cost_per_license,
            'notes' => $license->notes,
        ]);
    }

    public function update(UpdateLicenseRequest $request, License $license)
    {
        try {
            $updatedLicense = $this->licenseService->updateLicense($license, $request->validated());

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'License updated successfully',
                    'license' => $updatedLicense
                ]);
            }

            return redirect()->route('licenses.index')->with('success', 'License updated successfully');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 400);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy(License $license)
    {
        try {
            $this->licenseService->deleteLicense($license);

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'License deleted successfully'
                ]);
            }

            return redirect()->route('licenses.index')->with('success', 'License deleted successfully');
        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 400);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function assign(Request $request, License $license)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'expiry_date' => 'nullable|string',
        ]);

        try {
            $this->licenseService->assignLicense($license, $request->employee_id, $request->expiry_date);
            return response()->json(['success' => true, 'message' => 'License assigned successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function revoke(Request $request, License $license)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
        ]);

        try {
            $this->licenseService->revokeLicense($license, $request->employee_id);
            return response()->json(['success' => true, 'message' => 'License revoked successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
}
