<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Group extends Model
{
    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function specializations(): BelongsTo
    {
        return $this->belongsTo(Specialization::class, 'specialization_id');
    }

    public function practices(): HasMany
    {
        return $this->hasMany(Practice::class);
    }
}
