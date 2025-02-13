<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Specialization extends Model
{
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function groups(): HasMany
    {
        return $this->hasMany(Group::class);
    }
}
