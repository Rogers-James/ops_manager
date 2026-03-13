<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HrRequest extends Model
{
    use SoftDeletes;

    protected $table = 'hr_requests';

    protected $fillable = ['company_id','hr_request_type_id','employee_id','payload','status'];

    protected $casts = ['payload'=>'array'];

    public function company() { return $this->belongsTo(Company::class); }
    public function type() { return $this->belongsTo(HrRequestType::class,'hr_request_type_id'); }
    public function employee() { return $this->belongsTo(Employee::class); }
    public function approvalRequest() { return $this->morphOne(ApprovalRequest::class, 'request'); }
}
