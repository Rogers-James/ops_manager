<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\CostCenter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class CostCenterController extends Controller
{
    public function index()
    {
        $costCenters = CostCenter::orderBy('name')->paginate(15);
        return view('admin.pages.setup.cost-centers', compact('costCenters'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:cost_centers,name',
            'code' => 'nullable|string|max:30|unique:cost_centers,code',
        ]);

        try {
            CostCenter::create([
                // 'company_id' => auth()->user()->company_id ?? 1,
                'name' => $data['name'],
                'code' => $data['code'] ?? null,
            ]);

            return back()->with('success', 'Cost center created successfully!');
        } catch (Throwable $e) {
            Log::error('CostCenter create failed', ['message' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Something went wrong while creating cost center.');
        }
    }

    public function update(Request $request, CostCenter $costCenter)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:cost_centers,name,' . $costCenter->id,
            'code' => 'nullable|string|max:30|unique:cost_centers,code,' . $costCenter->id,
        ]);

        try {
            $costCenter->update($data);
            return back()->with('success', 'Cost center updated successfully!');
        } catch (Throwable $e) {
            Log::error('CostCenter update failed', ['message' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Something went wrong while updating cost center.');
        }
    }

    public function destroy(CostCenter $costCenter)
    {
        try {
            $costCenter->delete();
            return back()->with('success', 'Cost center deleted successfully!');
        } catch (Throwable $e) {
            Log::error('CostCenter delete failed', ['message' => $e->getMessage()]);
            return back()->with('error', 'Cannot delete cost center (it may be used in employees).');
        }
    }
}
