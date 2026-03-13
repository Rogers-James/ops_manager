<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class WorkflowCondition extends Model
{
    protected $fillable = ['company_id','workflow_id','rules'];

    protected $table = 'workflow_conditions';
    protected $casts = ['rules'=>'array'];
    public function company() { return $this->belongsTo(Company::class); }
    public function workflow() { return $this->belongsTo(Workflow::class); }

}
