<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class ClearanceChecklist extends Model
{
    protected $fillable = ['company_id','name'];

    protected $table = 'clearance_checklists';
    public function company() { return $this->belongsTo(Company::class); }
    public function items() { return $this->hasMany(ClearanceItem::class); }

}
