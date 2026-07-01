<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryOverride extends Model
{
    protected $fillable = [
        'major_id',
        'name',
    ];

    protected $casts = [
        'major_id' => 'integer',
    ];
}
