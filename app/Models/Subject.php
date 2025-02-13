<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Subject extends Model
{
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->using(SubjectUser::class)
            ->withPivot('active')
            ->withTimestamps();
    }

    public function practices(): HasMany
    {
        return $this->hasMany(Practice::class);
    }
}
