<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Designation;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Throwable;

class DesignationController extends Controller
{
    public function index()
    {
        $designations = Designation::withCount('employments')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.pages.setup.designations', compact('designations'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
        ]);

        try {
            Designation::create([
                // 'company_id' => Auth::user()->company_id ?? 1,
                'name' => $data['name'],
            ]);

            return back()->with('success', 'Designation added successfully!');
        } catch (QueryException $e) {
            if (($e->errorInfo[1] ?? null) == 1062) {
                return back()->withInput()->with('error', 'Designation already exists.');
            }
            return back()->withInput()->with('error', 'Database error while saving designation.');
        } catch (Throwable $e) {
            return back()->withInput()->with('error', 'Something went wrong.');
        }
    }

    public function update(Request $request, Designation $designation)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
        ]);

        try {
            $designation->update(['name' => $data['name']]);
            return back()->with('success', 'Designation updated successfully!');
        } catch (Throwable $e) {
            return back()->withInput()->with('error', 'Something went wrong.');
        }
    }

    public function destroy(Designation $designation)
    {
        try {
            if ($designation->employments()->exists()) {
                return back()->with('error', 'Cannot delete. Designation is linked with employee employment.');
            }

            $designation->delete();
            return back()->with('success', 'Designation deleted successfully!');
        } catch (QueryException $e) {
            if (($e->errorInfo[1] ?? null) == 1451) {
                return back()->with('error', 'Cannot delete. Designation is linked to other records.');
            }
            return back()->with('error', 'Database error while deleting designation.');
        } catch (Throwable $e) {
            return back()->with('error', 'Something went wrong.');
        }
    }
}
