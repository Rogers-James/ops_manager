<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Throwable;

class GradeController extends Controller
{
    public function index()
    {
        $grades = Grade::withCount('employments')
            ->orderBy('rank')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.pages.setup.grades', compact('grades'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'rank' => 'nullable|integer|min:0|max:9999',
        ]);

        try {
            Grade::create([
                'company_id' => Auth::user()->company_id ?? 1,
                'name' => $data['name'],
                'rank' => $data['rank'] ?? null,
            ]);

            return back()->with('success', 'Grade added successfully!');
        } catch (QueryException $e) {
            if (($e->errorInfo[1] ?? null) == 1062) {
                return back()->withInput()->with('error', 'Grade already exists.');
            }
            return back()->withInput()->with('error', 'Database error while saving grade.');
        } catch (Throwable $e) {
            return back()->withInput()->with('error', 'Something went wrong.');
        }
    }

    public function update(Request $request, Grade $grade)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'rank' => 'nullable|integer|min:0|max:9999',
        ]);

        try {
            $grade->update([
                'name' => $data['name'],
                'rank' => $data['rank'] ?? null,
            ]);

            return back()->with('success', 'Grade updated successfully!');
        } catch (Throwable $e) {
            return back()->withInput()->with('error', 'Something went wrong.');
        }
    }

    public function destroy(Grade $grade)
    {
        try {
            if ($grade->employments()->exists()) {
                return back()->with('error', 'Cannot delete. Grade is linked with employee employment.');
            }

            $grade->delete();
            return back()->with('success', 'Grade deleted successfully!');
        } catch (QueryException $e) {
            if (($e->errorInfo[1] ?? null) == 1451) {
                return back()->with('error', 'Cannot delete. Grade is linked to other records.');
            }
            return back()->with('error', 'Database error while deleting grade.');
        } catch (Throwable $e) {
            return back()->with('error', 'Something went wrong.');
        }
    }
}
