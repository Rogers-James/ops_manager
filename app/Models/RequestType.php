<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestType extends Model
{
    protected $table = 'request_types';

    protected $fillable = [
        'company_id',
        'name',
        'code',
        'workflow_id',
        'requires_document',
        'is_active',
    ];

    protected $casts = [
        'requires_document' => 'boolean',
        'is_active' => 'boolean',
    ];

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
