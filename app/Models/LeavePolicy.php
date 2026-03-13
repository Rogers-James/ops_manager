<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class LeavePolicy extends Model
{
    protected $fillable = ['company_id','name','accrual_method','carry_forward_allowed','max_carry_forward','encashment_allowed','count_weekends','count_holidays'];

    protected $table = 'leave_policies';
    public function company() { return $this->belongsTo(Company::class); }
    public function leaveTypes() { return $this->belongsToMany(LeaveType::class,'leave_policy_leave_types')->withPivot(['annual_quota','monthly_accrual','company_id'])->withTimestamps(); }
    public function employeeAssignments() { return $this->hasMany(EmployeeLeavePolicy::class); }

}
