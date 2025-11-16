<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Employee;
use App\Http\Requests\StoreAssetRequest;
use App\Http\Requests\UpdateAssetRequest;
use App\Services\AssetService;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    protected $assetService;

    public function __construct(AssetService $assetService)
    {
        $this->assetService = $assetService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['status', 'search']);
        $assets = $this->assetService->getAllAssets($filters);
        $employees = Employee::active()->get();

        return view('assets.index', compact('assets', 'employees'));
    }

    public function store(StoreAssetRequest $request)
    {
        try {
            $this->assetService->createAsset($request->validated());
            return redirect()->route('asset-management.index')->with('success', 'Asset created successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create asset');
        }
    }

    public function show(Asset $asset)
    {
        $asset->load('employee');

        return response()->json([
            'id' => $asset->id,
            'name' => $asset->name,
            'type' => $asset->type,
            'serial_number' => $asset->serial_number,
            'brand' => $asset->brand,
            'model' => $asset->model,
            'purchase_date' => $asset->purchase_date,
            'warranty_expiry' => $asset->warranty_expiry,
            'value' => $asset->value,
            'condition' => $asset->condition,
            'notes' => $asset->notes,
            'status' => $asset->status,
            'employee' => $asset->employee ? [
                'id' => $asset->employee->id,
                'name' => $asset->employee->name,
            ] : null,
        ]);
    }

    public function edit(Asset $asset)
    {
        return response()->json([
            'name' => $asset->name,
            'type' => $asset->type,
            'serial_number' => $asset->serial_number,
            'brand' => $asset->brand,
            'model' => $asset->model,
            'purchase_date' => $asset->purchase_date,
            'warranty_expiry' => $asset->warranty_expiry,
            'value' => $asset->value,
            'condition' => $asset->condition,
            'notes' => $asset->notes,
        ]);
    }

    public function update(UpdateAssetRequest $request, Asset $asset)
    {
        try {
            $this->assetService->updateAsset($asset, $request->validated());
            return redirect()->route('asset-management.index')->with('success', 'Asset updated successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update asset');
        }
    }

    public function destroy(Asset $asset)
    {
        try {
            $this->assetService->deleteAsset($asset);
            return redirect()->route('asset-management.index')->with('success', 'Asset deleted successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function assign(Request $request, Asset $asset)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
        ]);

        try {
            $this->assetService->assignAsset($asset, $request->employee_id);
            return response()->json(['message' => 'Asset assigned successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function unassign(Asset $asset)
    {
        try {
            $this->assetService->unassignAsset($asset);
            return response()->json(['message' => 'Asset unassigned successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
