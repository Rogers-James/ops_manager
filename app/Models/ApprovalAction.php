<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class ApprovalAction extends Model
{
    protected $fillable = ['company_id','approval_request_id','step_order','acted_by','action','comments'];

    protected $table = 'approval_actions';
    public function company() { return $this->belongsTo(Company::class); }
    public function approvalRequest() { return $this->belongsTo(ApprovalRequest::class); }
    public function actor() { return $this->belongsTo(User::class,'acted_by'); }

}
