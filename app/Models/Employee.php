<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'employee_code',
        'first_name',
        'last_name',
        'email',
        'phone',
        'dob',
        'gender',
        'marital_status',
        'photo_path',
        'status'
    ];

    // protected $casts = [
    //     'join_date' => 'date',
    // ];



    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function employment()
    {
        return $this->hasOne(EmployeeEmployment::class);
    }


    public function emergencyContacts()
    {
        return $this->hasMany(EmployeeEmergencyContact::class);
    }
    public function bankAccounts()
    {
        return $this->hasMany(EmployeeBankAccount::class);
    }
    public function addresses()
    {
        return $this->hasMany(EmployeeAddress::class);
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'owner');
    }

    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class);
    }
    public function attendanceRequests()
    {
        return $this->hasMany(AttendanceRequest::class);
    }
    public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLog::class);
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }
    public function leaveBalances()
    {
        return $this->hasMany(LeaveBalance::class);
    }
    public function leavePolicies()
    {
        return $this->hasMany(EmployeeLeavePolicy::class);
    }

    public function salaryStructures()
    {
        return $this->hasMany(EmployeeSalaryStructure::class);
    }
    public function recurringComponents()
    {
        return $this->hasMany(EmployeeRecurringComponent::class);
    }
    public function payrollItems()
    {
        return $this->hasMany(PayrollRunItem::class);
    }
    public function loans()
    {
        return $this->hasMany(EmployeeLoan::class);
    }

    public function finalSettlements()
    {
        return $this->hasMany(FinalSettlement::class);
    }

    public function shiftAssignments()
    {
        return $this->hasMany(ShiftGroupAssignment::class);
    }

    public function resignations()
    {
        return $this->hasMany(Resignation::class);
    }

    public function assetAssignments()
    {
        return $this->hasMany(AssetAssignment::class);
    }

    public function hrRequests()
    {
        return $this->hasMany(HrRequest::class);
    }
}
