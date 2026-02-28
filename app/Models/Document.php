<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    protected $fillable = ['student_id', 'document_name', 'file_path'];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
