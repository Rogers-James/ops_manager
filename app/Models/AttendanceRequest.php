<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttendanceRequest extends Model
{
    use SoftDeletes;

    protected $table = 'attendance_requests';

    protected $fillable = [
        'company_id',
        'employee_id',
        'date',
        'requested_first_in',
        'requested_last_out',
        'reason',
        'status',
        'workflow_id'
    ];

    protected $casts = [
        'date' => 'date',
        'requested_first_in' => 'datetime',
        'requested_last_out' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    public function workflow()
    {
        return $this->belongsTo(Workflow::class);
    }
    public function approvalRequest()
    {
        return $this->morphOne(ApprovalRequest::class, 'request');
    }
}
