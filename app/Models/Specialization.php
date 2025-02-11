<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class Specialization extends Model
{
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
