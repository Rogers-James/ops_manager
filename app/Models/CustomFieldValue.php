<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class CustomFieldValue extends Model
{
    protected $fillable = ['company_id','custom_field_id','entity_type','entity_id','value'];

    protected $table = 'custom_field_values';
    public function company() { return $this->belongsTo(Company::class); }
    public function field() { return $this->belongsTo(CustomField::class,'custom_field_id'); }
    public function entity() { return $this->morphTo(); }

}
