<?php

use App\Models\ShiftGroupAssignment;
use App\Models\Holiday;
use App\Models\WorkWeekProfile;
use Carbon\Carbon;

private function resolveShiftAndDayType(int $employeeId, string $date): array
{
    $companyId = 1;
    $d = Carbon::parse($date);

    // 1) Holiday?
    $isHoliday = Holiday::where('company_id', $companyId)
        ->whereDate('date', $d->toDateString())
        ->exists();

    // 2) Weekend? (Work week profile)
    // If you have company->work_week_profile_id then use that.
    // Otherwise pick default profile.
    $profile = WorkWeekProfile::where('company_id', $companyId)
        ->orderByDesc('is_default')
        ->first();

    $weekend = false;
    if ($profile) {
        // assume profile stores booleans: mon,tue,wed,thu,fri,sat,sun where true=working
        $dayKey = strtolower($d->format('D')); // mon,tue,...
        $weekend = (isset($profile->$dayKey) && !$profile->$dayKey);
    }

    // 3) Find shift assignment effective on date
    $assignment = ShiftGroupAssignment::with('group.defaultShiftType')
        ->where('company_id', $companyId)
        ->where('employee_id', $employeeId)
        ->whereDate('effective_from', '<=', $d->toDateString())
        ->where(function($q) use ($d){
            $q->whereNull('effective_to')
              ->orWhereDate('effective_to', '>=', $d->toDateString());
        })
        ->latest('effective_from')
        ->first();

    $shiftTypeId = $assignment?->group?->default_shift_type_id;

    return [
        'shift_type_id' => $shiftTypeId,
        'is_holiday' => $isHoliday,
        'is_weekend' => $weekend,
    ];
}
