<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class WorkflowStep extends Model
{
    protected $fillable = ['company_id','workflow_id','step_order','approver_type','approver_role_id','approver_user_id','min_approvals'];

    protected $table = 'workflow_steps';
    public function company() { return $this->belongsTo(Company::class); }
    public function workflow() { return $this->belongsTo(Workflow::class); }
    public function approverRole() { return $this->belongsTo(Role::class,'approver_role_id'); }
    public function approverUser() { return $this->belongsTo(User::class,'approver_user_id'); }

}
