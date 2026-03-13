<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = ['company_id', 'document_type_id', 'owner_type', 'owner_id', 'title', 'file_path', 'expires_at', 'meta'];

    protected $casts = ['meta' => 'array', 'expires_at' => 'date'];
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function type()
    {
        return $this->belongsTo(DocumentType::class, 'document_type_id');
    }
    public function owner()
    {
        return $this->morphTo();
    }
}
