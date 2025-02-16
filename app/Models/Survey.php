<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

final class Survey extends Model
{
    public function practice(): BelongsTo
    {
        return $this->belongsTo(Practice::class);
    }

    public function user(): HasOneThrough
    {
        return $this->hasOneThrough(User::class, Practice::class, 'id', 'id', 'practice_id', 'user_id');
    }

    public function group(): HasOneThrough
    {
        return $this->hasOneThrough(Group::class, Practice::class, 'id', 'id', 'practice_id', 'group_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class);
    }
}
