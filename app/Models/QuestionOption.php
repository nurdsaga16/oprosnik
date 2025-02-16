<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class QuestionOption extends Model
{
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
