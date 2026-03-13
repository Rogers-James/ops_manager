<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Throwable;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::withCount('employments')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.pages.setup.locations', compact('locations'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'address' => 'nullable|string|max:255',
        ]);

        try {
            Location::create([
                // 'company_id' => Auth::user()->company_id ?? 1,
                'name' => $data['name'],
                'address' => $data['address'] ?? null,
            ]);

            return back()->with('success', 'Location added successfully!');
        } catch (QueryException $e) {
            if (($e->errorInfo[1] ?? null) == 1062) {
                return back()->withInput()->with('error', 'Location already exists.');
            }
            return back()->withInput()->with('error', 'Database error while saving location.');
        } catch (Throwable $e) {
            return back()->withInput()->with('error', 'Something went wrong.');
        }
    }

    public function update(Request $request, Location $location)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'address' => 'nullable|string|max:255',
        ]);

        try {
            $location->update([
                'name' => $data['name'],
                'address' => $data['address'] ?? null,
            ]);

            return back()->with('success', 'Location updated successfully!');
        } catch (Throwable $e) {
            return back()->withInput()->with('error', 'Something went wrong.');
        }
    }

    public function destroy(Location $location)
    {
        try {
            if ($location->employments()->exists()) {
                return back()->with('error', 'Cannot delete. Location is linked with employee employment.');
            }

            $location->delete();
            return back()->with('success', 'Location deleted successfully!');
        } catch (QueryException $e) {
            if (($e->errorInfo[1] ?? null) == 1451) {
                return back()->with('error', 'Cannot delete. Location is linked to other records.');
            }
            return back()->with('error', 'Database error while deleting location.');
        } catch (Throwable $e) {
            return back()->with('error', 'Something went wrong.');
        }
    }
}
