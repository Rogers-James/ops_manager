<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class LeaveRequestDay extends Model
{
    protected $fillable = ['company_id','leave_request_id','date','unit'];

    protected $table = 'leave_request_days';
    protected $casts = ['date'=>'date'];
    public function company() { return $this->belongsTo(Company::class); }
    public function request() { return $this->belongsTo(LeaveRequest::class,'leave_request_id'); }

}
