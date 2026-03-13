<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'name',
        'legal_name',
        'website',
        'email',
        'phone',
        'timezone',
        'currency_code',
        'date_format',
        'logo_path',
        'hq_address',
        'city',
        'state',
        'postal_code',
        'country',
        'registration_no',
        'tax_id'
    ];


    public function assets()
    {
        return $this->hasMany(Asset::class);
    }

    public function assetAssignments()
    {
        return $this->hasMany(AssetAssignment::class);
    }

    public function hrRequestTypes()
    {
        return $this->hasMany(HrRequestType::class);
    }

    public function hrRequests()
    {
        return $this->hasMany(HrRequest::class);
    }

    public function locations()
    {
        return $this->hasMany(Location::class);
    }
    public function departments()
    {
        return $this->hasMany(Department::class);
    }
    public function designations()
    {
        return $this->hasMany(Designation::class);
    }
    public function grades()
    {
        return $this->hasMany(Grade::class);
    }
    public function costCenters()
    {
        return $this->hasMany(CostCenter::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function holidayCalendars()
    {
        return $this->hasMany(HolidayCalendar::class);
    }
    public function workWeekProfiles()
    {
        return $this->hasMany(WorkWeekProfile::class);
    }
    public function shiftTypes()
    {
        return $this->hasMany(ShiftType::class);
    }
    public function shiftGroups()
    {
        return $this->hasMany(ShiftGroup::class);
    }

    public function leaveTypes()
    {
        return $this->hasMany(LeaveType::class);
    }
    public function leavePolicies()
    {
        return $this->hasMany(LeavePolicy::class);
    }

    public function workflows()
    {
        return $this->hasMany(Workflow::class);
    }

    public function paySchedules()
    {
        return $this->hasMany(PaySchedule::class);
    }
    public function salaryComponents()
    {
        return $this->hasMany(SalaryComponent::class);
    }
    public function salaryStructures()
    {
        return $this->hasMany(SalaryStructure::class);
    }
    public function payrollRuns()
    {
        return $this->hasMany(PayrollRun::class);
    }

    public function assetCategories()
    {
        return $this->hasMany(AssetCategory::class);
    }
    public function templates()
    {
        return $this->hasMany(Template::class);
    }
}
