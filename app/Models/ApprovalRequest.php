<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class ApprovalRequest extends Model
{
    protected $fillable = ['company_id','workflow_id','request_type','request_id','current_step','status'];

    protected $table = 'approval_requests';
    public function company() { return $this->belongsTo(Company::class); }
    public function workflow() { return $this->belongsTo(Workflow::class); }
    public function request() { return $this->morphTo(); }
    public function actions() { return $this->hasMany(ApprovalAction::class); }

}
