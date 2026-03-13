<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class CustomField extends Model
{
    protected $fillable = ['company_id','module','key','label','type','is_required','validation'];

    protected $table = 'custom_fields';
    protected $casts = ['validation'=>'array'];
    public function company() { return $this->belongsTo(Company::class); }
    public function options() { return $this->hasMany(CustomFieldOption::class); }
    public function values() { return $this->hasMany(CustomFieldValue::class); }

}
