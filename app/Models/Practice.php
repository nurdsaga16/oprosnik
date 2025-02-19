<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

final class Practice extends Model
{
    protected $table = 'practices';

    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'subject_id',
        'user_id',
        'group_id',
    ];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function survey(): HasOne
    {
        return $this->hasOne(Survey::class, 'practice_id');
    }
}
