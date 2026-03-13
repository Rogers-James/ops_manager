<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    protected $fillable = [
        'company_id',
        'name',
        'type',
        'subject',
        'content',
        'is_active',
    ];

    protected $table = 'templates';
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
