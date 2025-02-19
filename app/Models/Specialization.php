<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Specialization extends Model
{
    protected $table = 'specializations';

    protected $fillable = [
        'title',
        'department_id',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function groups(): HasMany
    {
        return $this->hasMany(Group::class);
    }
}
