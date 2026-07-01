<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgressLog extends Model
{
    use HasFactory;

    public const STATUS_NOT_STARTED = 0;

    public const STATUS_IN_PROGRESS = 1;

    public const STATUS_COMPLETED = 2;

    protected $fillable = [
        'user_id',
        'textbook_id',
        'status',
        'is_flagged',
        'memo',
    ];

    protected $casts = [
        'status' => 'integer',
        'is_flagged' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function textbook(): BelongsTo
    {
        return $this->belongsTo(Textbook::class);
    }
}
