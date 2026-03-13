<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExitClearanceTask extends Model
{
    protected $fillable = [
        'company_id',
        'exit_clearance_id',
        'module',
        'title',
        'notes',
        'status',
        'action_by',
        'action_at',
    ];

    protected $casts = [
        'action_at' => 'datetime',
    ];

    public function clearance()
    {
        return $this->belongsTo(ExitClearance::class, 'exit_clearance_id');
    }

    public function actionBy()
    {
        return $this->belongsTo(User::class, 'action_by');
    }
}
