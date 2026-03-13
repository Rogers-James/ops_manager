<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Throwable;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::with('parent')
            ->withCount('children')
            ->withCount('employments')
            ->paginate(15);

        // for dropdown (parent select)
        $parents = Department::orderBy('name')->get(['id', 'name']);

        return view('admin.pages.setup.departments', compact('departments', 'parents'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'parent_id' => 'nullable|exists:departments,id',
        ]);

        try {
            Department::create([
                // 'company_id' => Auth::user()->company_id ?? 1, // keep for SaaS later
                'name' => $data['name'],
                'parent_id' => $data['parent_id'] ?? null,
            ]);

            return back()->with('success', 'Department added successfully!');
        } catch (QueryException $e) {
            // duplicate (if you have unique index)
            if (($e->errorInfo[1] ?? null) == 1062) {
                return back()->withInput()->with('error', 'Department already exists.');
            }
            return back()->withInput()->with('error', 'Database error while saving department.');
        } catch (Throwable $e) {
            return back()->withInput()->with('error', 'Something went wrong.');
        }
    }

    public function update(Request $request, Department $department)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'parent_id' => 'nullable|exists:departments,id',
        ]);

        // prevent parent = self
        if (!empty($data['parent_id']) && (int)$data['parent_id'] === (int)$department->id) {
            return back()->with('error', 'A department cannot be its own parent.');
        }

        try {
            $department->update([
                'name' => $data['name'],
                'parent_id' => $data['parent_id'] ?? null,
            ]);

            return back()->with('success', 'Department updated successfully!');
        } catch (QueryException $e) {
            if (($e->errorInfo[1] ?? null) == 1062) {
                return back()->withInput()->with('error', 'Department already exists.');
            }
            return back()->withInput()->with('error', 'Database error while updating department.');
        } catch (Throwable $e) {
            return back()->withInput()->with('error', 'Something went wrong.');
        }
    }

    public function destroy(Department $department)
    {
        try {
            // protect if has children
            if ($department->children()->exists()) {
                return back()->with('error', 'Cannot delete. This department has sub-departments.');
            }

            // protect if used in employment
            if ($department->employments()->exists()) {
                return back()->with('error', 'Cannot delete. This department is linked with employee employment.');
            }

            $department->delete();
            return back()->with('success', 'Department deleted successfully!');
        } catch (QueryException $e) {
            if (($e->errorInfo[1] ?? null) == 1451) {
                return back()->with('error', 'Cannot delete. Department is linked to other records.');
            }
            return back()->with('error', 'Database error while deleting department.');
        } catch (Throwable $e) {
            return back()->with('error', 'Something went wrong.');
        }
    }
}
