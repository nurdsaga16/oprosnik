<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Section extends Model
{
    protected $table = 'sections';

    protected $fillable = [
        'title',
        'description',
        'order',
        'survey_id',
    ];

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class, 'survey_id');
    }
}
