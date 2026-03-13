<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\AssetCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class AssetCategoryController extends Controller
{
    public function index()
    {
        $categories = AssetCategory::latest()->paginate(15);
        return view('admin.pages.operations.asset_categories', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:asset_categories,name',
        ]);

        try {
            AssetCategory::create([
                'company_id' => null,
                'name' => $data['name'],
            ]);

            return back()->with('success', 'Asset category created successfully.');
        } catch (Throwable $e) {
            Log::error('AssetCategory store failed', ['message' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Unable to create asset category.');
        }
    }

    public function update(Request $request, AssetCategory $assetCategory)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:asset_categories,name,' . $assetCategory->id,
        ]);

        try {
            $assetCategory->update([
                'name' => $data['name'],
            ]);

            return back()->with('success', 'Asset category updated successfully.');
        } catch (Throwable $e) {
            Log::error('AssetCategory update failed', ['message' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Unable to update asset category.');
        }
    }

    public function destroy(AssetCategory $assetCategory)
    {
        try {
            $assetCategory->delete();
            return back()->with('success', 'Asset category deleted successfully.');
        } catch (Throwable $e) {
            Log::error('AssetCategory delete failed', ['message' => $e->getMessage()]);
            return back()->with('error', 'Unable to delete asset category.');
        }
    }
}
