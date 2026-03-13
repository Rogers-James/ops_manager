<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class LeaveBalance extends Model
{
    protected $fillable = ['company_id','employee_id','leave_type_id','year','opening','accrued','used','adjusted','closing'];

    protected $table = 'leave_balances';
    public function company() { return $this->belongsTo(Company::class); }
    public function employee() { return $this->belongsTo(Employee::class); }
    public function leaveType() { return $this->belongsTo(LeaveType::class,'leave_type_id'); }

}
