<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolClass extends Model
{
    protected $table = 'classes';

    protected $fillable = ['class_name'];

    /** Class order for Nursery -> 12 (works in MySQL & PostgreSQL) */
    public function scopeOrderByClassOrder(Builder $query): Builder
    {
        $sql = "CASE class_name
            WHEN 'Nursery' THEN 1 WHEN 'LKG' THEN 2 WHEN 'UKG' THEN 3
            WHEN '1' THEN 4 WHEN '2' THEN 5 WHEN '3' THEN 6 WHEN '4' THEN 7
            WHEN '5' THEN 8 WHEN '6' THEN 9 WHEN '7' THEN 10 WHEN '8' THEN 11
            WHEN '9' THEN 12 WHEN '10' THEN 13 WHEN '11' THEN 14 WHEN '12' THEN 15
            ELSE 99 END";
        return $query->orderByRaw($sql);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class, 'class_id');
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class, 'class_id');
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'class_id');
    }
}
