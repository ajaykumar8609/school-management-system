<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Section extends Model
{
    public const ALLOWED_SECTIONS = ['A', 'B', 'C'];

    protected $fillable = ['class_id', 'section_name'];

    public function scopeOnlyABC($query)
    {
        return $query->whereIn('section_name', self::ALLOWED_SECTIONS);
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'section_id');
    }
}
