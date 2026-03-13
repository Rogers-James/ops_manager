<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use App\Models\HolidayCalendar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class HolidayController extends Controller
{
    public function index(Request $request)
    {
        $companyId = 1;
        $calendarId = $request->get('holiday_calendar_id');

        $calendars = HolidayCalendar::orderBy('name')->get();

        $query = Holiday::with('calendar')
            // ->where('company_id', $companyId)
            ->latest('date');

        if ($calendarId) {
            $query->where('holiday_calendar_id', $calendarId);
        }

        $holidays = $query->paginate(20)->withQueryString();

        return view('admin.pages.shifts.holidays', compact('holidays', 'calendars', 'calendarId'));
    }

    public function store(Request $request)
    {
        $companyId = 1;

        $data = $request->validate([
            'holiday_calendar_id' => 'required|exists:holiday_calendars,id',
            'date' => 'required|date',
            'name' => 'required|string|max:150',
            'is_paid' => 'nullable|boolean',
        ]);

        try {
            Holiday::create([
                // 'company_id' => $companyId,
                'holiday_calendar_id' => $data['holiday_calendar_id'],
                'date' => $data['date'],
                'name' => $data['name'],
                'is_paid' => (bool)($data['is_paid'] ?? true),
            ]);

            return back()->with('success', 'Holiday added.');
        } catch (Throwable $e) {
            Log::error('Holiday store failed', ['message' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Unable to add holiday.');
        }
    }

    public function destroy(Holiday $holiday)
    {
        try {
            $holiday->delete();
            return back()->with('success', 'Holiday deleted.');
        } catch (Throwable $e) {
            Log::error('Holiday delete failed', ['message' => $e->getMessage()]);
            return back()->with('error', 'Unable to delete holiday.');
        }
    }
}
