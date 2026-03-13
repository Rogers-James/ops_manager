<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Workflow extends Model
{
    protected $fillable = ['company_id', 'module', 'name', 'is_active'];

    protected $table = 'workflows';
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function steps()
    {
        return $this->hasMany(WorkflowStep::class);
    }
    public function conditions()
    {
        return $this->hasMany(WorkflowCondition::class);
    }
    public function approvalRequests()
    {
        return $this->hasMany(ApprovalRequest::class);
    }
    public function hrRequestTypes()
    {
        return $this->hasMany(HrRequestType::class);
    }
}
