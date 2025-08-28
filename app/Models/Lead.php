<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'status',
        'registration_date',
    ];

    protected $casts = [
        // 'registration_date' => 'datetime',
        'status' => \App\StatusEnum::class,
    ];
}
