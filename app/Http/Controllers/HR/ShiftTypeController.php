<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\ShiftType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class ShiftTypeController extends Controller
{
    public function index()
    {
        $companyId = 1;

        $shiftTypes = ShiftType::latest()->paginate(15);

        return view('admin.pages.shifts.shift_types', compact('shiftTypes'));
    }

    public function store(Request $request)
    {
        $companyId = 1;

        $data = $request->validate([
            'name' => 'required|string|max:120',
            'start_time' => 'required', // time
            'end_time' => 'required',   // time
            'grace_in_minutes' => 'nullable|integer|min:0|max:300',
            'break_minutes' => 'nullable|integer|min:0|max:600',
        ]);

        try {
            ShiftType::create([
                // 'company_id' => $companyId,
                'name' => $data['name'],
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'grace_in_minutes' => $data['grace_in_minutes'] ?? 0,
                'break_minutes' => $data['break_minutes'] ?? 0,
            ]);

            return back()->with('success', 'Shift type created.');
        } catch (Throwable $e) {
            Log::error('ShiftType store failed', ['message' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Unable to create shift type.');
        }
    }

    public function update(Request $request, ShiftType $shiftType)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'start_time' => 'required',
            'end_time' => 'required',
            'grace_in_minutes' => 'nullable|integer|min:0|max:300',
            'break_minutes' => 'nullable|integer|min:0|max:600',
        ]);

        try {
            $shiftType->update([
                'name' => $data['name'],
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'grace_in_minutes' => $data['grace_in_minutes'] ?? 0,
                'break_minutes' => $data['break_minutes'] ?? 0,
            ]);

            return back()->with('success', 'Shift type updated.');
        } catch (Throwable $e) {
            Log::error('ShiftType update failed', ['message' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Unable to update shift type.');
        }
    }

    public function destroy(ShiftType $shiftType)
    {
        try {
            $shiftType->delete();
            return back()->with('success', 'Shift type deleted.');
        } catch (Throwable $e) {
            Log::error('ShiftType delete failed', ['message' => $e->getMessage()]);
            return back()->with('error', 'Unable to delete shift type.');
        }
    }
}
