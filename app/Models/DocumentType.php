<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    protected $fillable = ['company_id', 'name', 'required'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function documents()
    {
        return $this->hasMany(Document::class);
    }
}
