<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class Department extends Model
{
    public function specializations()
    {
        return $this->hasMany(Specialization::class);
    }
}
