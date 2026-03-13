<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class SalaryStructureItem extends Model
{
    protected $fillable = ['company_id','salary_structure_id','salary_component_id','amount','meta'];

    protected $table = 'salary_structure_items';
    protected $casts = ['meta'=>'array'];
    public function company() { return $this->belongsTo(Company::class); }
    public function structure() { return $this->belongsTo(SalaryStructure::class,'salary_structure_id'); }
    public function component() { return $this->belongsTo(SalaryComponent::class,'salary_component_id'); }

}
