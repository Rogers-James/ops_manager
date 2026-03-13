<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\ShiftGroup;
use App\Models\ShiftType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class ShiftGroupController extends Controller
{
    public function index()
    {
        $companyId = 1;

        $shiftTypes = ShiftType::orderBy('name')->get();
        $shiftGroups = ShiftGroup::with('defaultShiftType')
            // ->where('company_id', $companyId)
            ->latest()->paginate(15);

        return view('admin.pages.shifts.shift_groups', compact('shiftGroups', 'shiftTypes'));
    }

    public function store(Request $request)
    {
        $companyId = 1;

        $data = $request->validate([
            'name' => 'required|string|max:120',
            'default_shift_type_id' => 'required|exists:shift_types,id',
        ]);

        try {
            ShiftGroup::create([
                // 'company_id' => $companyId,
                'name' => $data['name'],
                'default_shift_type_id' => $data['default_shift_type_id'],
            ]);

            return back()->with('success', 'Shift group created.');
        } catch (Throwable $e) {
            Log::error('ShiftGroup store failed', ['message' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Unable to create shift group.');
        }
    }

    public function update(Request $request, ShiftGroup $shiftGroup)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'default_shift_type_id' => 'required|exists:shift_types,id',
        ]);

        try {
            $shiftGroup->update([
                'name' => $data['name'],
                'default_shift_type_id' => $data['default_shift_type_id'],
            ]);

            return back()->with('success', 'Shift group updated.');
        } catch (Throwable $e) {
            Log::error('ShiftGroup update failed', ['message' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Unable to update shift group.');
        }
    }

    public function destroy(ShiftGroup $shiftGroup)
    {
        try {
            $shiftGroup->delete();
            return back()->with('success', 'Shift group deleted.');
        } catch (Throwable $e) {
            Log::error('ShiftGroup delete failed', ['message' => $e->getMessage()]);
            return back()->with('error', 'Unable to delete shift group.');
        }
    }
}
