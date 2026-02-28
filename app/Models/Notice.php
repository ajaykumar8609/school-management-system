<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    protected $fillable = ['title', 'description', 'notice_date', 'end_date', 'is_active'];

    protected $casts = [
        'notice_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];
}
