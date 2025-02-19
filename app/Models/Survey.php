<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

final class Survey extends Model
{
    protected $table = 'surveys';

    protected $fillable = [
        'title',
        'description',
        'response_limit',
        'start_date',
        'end_date',
        'practice_id',
        'status',
        'template',
    ];

    public function practice(): BelongsTo
    {
        return $this->belongsTo(Practice::class, 'practice_id');
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

    public function surveyResponses(): HasMany
    {
        return $this->hasMany(SurveyResponse::class);
    }
}
