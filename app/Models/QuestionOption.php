<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

final class QuestionOption extends Model
{
    protected $table = 'question_options';

    protected $fillable = [
        'option',
        'order',
        'question_id',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class, 'question_id');
    }

    public function survey(): HasOneThrough
    {
        return $this->hasOneThrough(Survey::class, Question::class, 'survey_id', 'id', 'id', 'id');
    }

    public function section(): HasOneThrough
    {
        return $this->hasOneThrough(Section::class, Question::class, 'section_id', 'id', 'id', 'id');
    }
}
