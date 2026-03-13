<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrRequestType extends Model
{
    protected $fillable = ['company_id', 'name', 'code', 'workflow_id'];

    protected $table = 'hr_request_types';
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function workflow()
    {
        return $this->belongsTo(Workflow::class);
    }
    public function requests()
    {
        return $this->hasMany(HrRequest::class, 'hr_request_type_id');
    }
}
