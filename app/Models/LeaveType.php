<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class LeaveType extends Model
{
    protected $fillable = ['company_id','name','code','is_paid','requires_attachment'];

    protected $table = 'leave_types';
    public function company() { return $this->belongsTo(Company::class); }
    public function requests() { return $this->hasMany(LeaveRequest::class,'leave_type_id'); }

}
