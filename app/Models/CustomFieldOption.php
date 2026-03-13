<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class CustomFieldOption extends Model
{
    protected $fillable = ['company_id','custom_field_id','value','label'];

    protected $table = 'custom_field_options';
    public function company() { return $this->belongsTo(Company::class); }
    public function field() { return $this->belongsTo(CustomField::class,'custom_field_id'); }

}
