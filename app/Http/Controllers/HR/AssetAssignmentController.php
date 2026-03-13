<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class AssetAssignmentController extends Controller
{
    public function index()
    {
        $employees = Employee::orderBy('first_name')->get(['id','employee_code','first_name','last_name']);

        $assets = Asset::whereIn('status', ['available', 'assigned'])
            ->orderBy('name')
            ->get();

        $assignments = AssetAssignment::with(['asset.category', 'employee'])
            ->latest()
            ->paginate(15);

        return view('admin.pages.operations.asset_assignments', compact('employees', 'assets', 'assignments'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'employee_id' => 'required|exists:employees,id',
            'assigned_at' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $asset = Asset::findOrFail($data['asset_id']);

            $alreadyAssigned = AssetAssignment::where('asset_id', $asset->id)
                ->whereNull('returned_at')
                ->exists();

            if ($alreadyAssigned) {
                DB::rollBack();
                return back()->withInput()->with('error', 'This asset is already assigned.');
            }

            AssetAssignment::create([
                'company_id' => null,
                'asset_id' => $data['asset_id'],
                'employee_id' => $data['employee_id'],
                'assigned_at' => $data['assigned_at'],
                'returned_at' => null,
                'notes' => $data['notes'] ?? null,
            ]);

            $asset->update(['status' => 'assigned']);

            DB::commit();
            return back()->with('success', 'Asset assigned successfully.');
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('AssetAssignment store failed', ['message' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Unable to assign asset.');
        }
    }

    public function returnAsset(Request $request, AssetAssignment $assetAssignment)
    {
        $data = $request->validate([
            'returned_at' => 'required|date|after_or_equal:assigned_at',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $assetAssignment->update([
                'returned_at' => $data['returned_at'],
                'notes' => $data['notes'] ?? $assetAssignment->notes,
            ]);

            $assetAssignment->asset->update([
                'status' => 'available',
            ]);

            DB::commit();
            return back()->with('success', 'Asset returned successfully.');
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('AssetAssignment return failed', ['message' => $e->getMessage()]);
            return back()->with('error', 'Unable to return asset.');
        }
    }

    public function destroy(AssetAssignment $assetAssignment)
    {
        try {
            $assetAssignment->delete();
            return back()->with('success', 'Assignment deleted successfully.');
        } catch (Throwable $e) {
            Log::error('AssetAssignment delete failed', ['message' => $e->getMessage()]);
            return back()->with('error', 'Unable to delete assignment.');
        }
    }
}
