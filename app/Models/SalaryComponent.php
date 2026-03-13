<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class SalaryComponent extends Model
{
    protected $fillable = ['company_id','name','code','type','is_taxable','is_statutory','calculation_type','formula'];

    protected $table = 'salary_components';
    public function company() { return $this->belongsTo(Company::class); }
    public function structureItems() { return $this->hasMany(SalaryStructureItem::class); }

}
