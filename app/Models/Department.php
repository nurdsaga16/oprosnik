<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Department extends Model
{
    protected $table = 'departments';

    protected $fillable = [
        'name',
    ];

    public function specializations(): HasMany
    {
        return $this->hasMany(Specialization::class);
    }
}
