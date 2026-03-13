<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class AssetController extends Controller
{
    public function index()
    {
        $categories = AssetCategory::orderBy('name')->get();

        $assets = Asset::with(['category', 'activeAssignment.employee'])
            ->latest()
            ->paginate(15);

        return view('admin.pages.operations.assets', compact('assets', 'categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'asset_category_id' => 'required|exists:asset_categories,id',
            'tag' => 'required|string|max:255|unique:assets,tag',
            'name' => 'required|string|max:255',
            'serial_no' => 'nullable|string|max:255',
            'status' => 'required|in:available,assigned,repair,retired',
        ]);

        try {
            Asset::create([
                'company_id' => null,
                'asset_category_id' => $data['asset_category_id'],
                'tag' => $data['tag'],
                'name' => $data['name'],
                'serial_no' => $data['serial_no'] ?? null,
                'status' => $data['status'],
            ]);

            return back()->with('success', 'Asset created successfully.');
        } catch (Throwable $e) {
            Log::error('Asset store failed', ['message' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Unable to create asset.');
        }
    }

    public function update(Request $request, Asset $asset)
    {
        $data = $request->validate([
            'asset_category_id' => 'required|exists:asset_categories,id',
            'tag' => 'required|string|max:255|unique:assets,tag,' . $asset->id,
            'name' => 'required|string|max:255',
            'serial_no' => 'nullable|string|max:255',
            'status' => 'required|in:available,assigned,repair,retired',
        ]);

        try {
            $asset->update([
                'asset_category_id' => $data['asset_category_id'],
                'tag' => $data['tag'],
                'name' => $data['name'],
                'serial_no' => $data['serial_no'] ?? null,
                'status' => $data['status'],
            ]);

            return back()->with('success', 'Asset updated successfully.');
        } catch (Throwable $e) {
            Log::error('Asset update failed', ['message' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Unable to update asset.');
        }
    }

    public function destroy(Asset $asset)
    {
        try {
            $asset->delete();
            return back()->with('success', 'Asset deleted successfully.');
        } catch (Throwable $e) {
            Log::error('Asset delete failed', ['message' => $e->getMessage()]);
            return back()->with('error', 'Unable to delete asset.');
        }
    }
}
