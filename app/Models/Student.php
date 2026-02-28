<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    protected $fillable = [
        'admission_no', 'roll_no', 'first_name', 'last_name', 'gender', 'dob',
        'blood_group', 'category', 'class_id', 'section_id', 'father_name', 'mother_name',
        'parent_contact', 'alt_contact', 'email', 'current_address', 'permanent_address',
        'photo', 'status', 'admission_date'
    ];

    protected $casts = [
        'dob' => 'date',
        'admission_date' => 'date',
        'status' => 'boolean',
    ];

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function marks(): HasMany
    {
        return $this->hasMany(Mark::class);
    }

    public function attendance(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function fees(): HasMany
    {
        return $this->hasMany(Fee::class);
    }

    public function feePayments(): HasMany
    {
        return $this->hasMany(FeePayment::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getFeeStatusAttribute(): string
    {
        $totalFee = $this->relationLoaded('fees') ? $this->fees->sum('final_amount') : $this->fees()->sum('final_amount');
        $paid = $this->relationLoaded('feePayments') ? $this->feePayments->sum('amount') : $this->feePayments()->sum('amount');
        if ($totalFee <= 0) return 'Due';
        if ($paid >= $totalFee) return 'Paid';
        if ($paid > 0) return 'Partial';
        return 'Due';
    }
}
