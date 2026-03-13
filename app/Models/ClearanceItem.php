<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class ClearanceItem extends Model
{
    protected $fillable = ['company_id','clearance_checklist_id','department_id','title'];

    protected $table = 'clearance_items';
    public function company() { return $this->belongsTo(Company::class); }
    public function checklist() { return $this->belongsTo(ClearanceChecklist::class,'clearance_checklist_id'); }
    public function department() { return $this->belongsTo(Department::class); }

}
