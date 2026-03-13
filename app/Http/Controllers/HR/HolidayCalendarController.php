<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\HolidayCalendar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class HolidayCalendarController extends Controller
{
    public function index()
    {
        $companyId = 1;

        $calendars = HolidayCalendar::latest()->paginate(15);
        return view('admin.pages.shifts.holiday_calendars', compact('calendars'));
    }

    public function store(Request $request)
    {
        $companyId = 1;

        $data = $request->validate([
            'name' => 'required|string|max:150',
        ]);

        try {
            HolidayCalendar::create([
                // 'company_id' => $companyId,
                'name' => $data['name'],
            ]);

            return back()->with('success', 'Holiday calendar created.');
        } catch (Throwable $e) {
            Log::error('HolidayCalendar store failed', ['message' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Unable to create calendar.');
        }
    }

    public function update(Request $request, HolidayCalendar $calendar)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
        ]);

        try {
            $calendar->update(['name' => $data['name']]);
            return back()->with('success', 'Holiday calendar updated.');
        } catch (Throwable $e) {
            Log::error('HolidayCalendar update failed', ['message' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Unable to update calendar.');
        }
    }

    public function destroy(HolidayCalendar $calendar)
    {
        try {
            $calendar->delete();
            return back()->with('success', 'Holiday calendar deleted.');
        } catch (Throwable $e) {
            Log::error('HolidayCalendar delete failed', ['message' => $e->getMessage()]);
            return back()->with('error', 'Unable to delete calendar.');
        }
    }
}
