<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExitClearance extends Model
{
    protected $fillable = [
        'company_id',
        'resignation_id',
        'initiated_on',
        'status',
    ];

    protected $casts = [
        'initiated_on' => 'date',
    ];

    public function resignation()
    {
        return $this->belongsTo(Resignation::class);
    }

    public function tasks()
    {
        return $this->hasMany(ExitClearanceTask::class);
    }

    // helper: all tasks approved?
    public function allApproved(): bool
    {
        return $this->tasks()->where('status', '!=', 'approved')->count() === 0;
    }
}
