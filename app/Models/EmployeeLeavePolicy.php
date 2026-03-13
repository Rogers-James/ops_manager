<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class EmployeeLeavePolicy extends Model
{
    protected $fillable = ['company_id','employee_id','leave_policy_id','effective_from','effective_to'];

    protected $table = 'employee_leave_policies';
    public function company() { return $this->belongsTo(Company::class); }
    public function employee() { return $this->belongsTo(Employee::class); }
    public function policy() { return $this->belongsTo(LeavePolicy::class,'leave_policy_id'); }

}
