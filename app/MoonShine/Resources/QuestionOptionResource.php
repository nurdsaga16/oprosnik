<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Models\QuestionOption;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Enums\Action;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\Enums\JsEvent;
use MoonShine\Support\Enums\PageType;
use MoonShine\Support\Enums\SortDirection;
use MoonShine\Support\ListOf;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Text;

/**
 * @extends ModelResource<QuestionOption>
 */
final class QuestionOptionResource extends ModelResource
{
    protected string $model = QuestionOption::class;

    protected string $title = 'Варианты ответов';

    protected array $with = ['question', 'section', 'survey'];

    protected int $itemsPerPage = 10;

    protected bool $cursorPaginate = true;

    protected bool $columnSelection = true;

    protected SortDirection $sortDirection = SortDirection::ASC;

    protected ?PageType $redirectAfterSave = PageType::INDEX;

    /**
     * @return list<FieldContract>
     */
    protected function topButtons(): ListOf
    {
        return parent::topButtons()->add(
            ActionButton::make('Перезагрузить', '#')
                ->dispatchEvent(AlpineJs::event(JsEvent::TABLE_UPDATED, $this->getListComponentName()))
        );
    }

    protected function activeActions(): ListOf
    {
        return parent::activeActions()->except(Action::VIEW);
    }

    /**
     * @return list<FieldContract>
     */
    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('Вариант', 'option'),
            Number::make('Номер порядка', 'order')->sortable(),
            BelongsTo::make('Вопрос', 'question', 'question', QuestionResource::class)->sortable(),
            BelongsTo::make('Секция', 'section', 'title', SectionResource::class)->sortable(),
            BelongsTo::make('Опрос', 'survey', 'title', SurveyResource::class)->sortable(),
        ];
    }

    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function formFields(): iterable
    {
        return [
            Box::make([
                ID::make(),
                Text::make('Вариант', 'option')->required(),
                Number::make('Номер порядка', 'order')->required(),
                BelongsTo::make('Вопрос', 'question', 'question', QuestionResource::class)
                    ->required()
                    ->searchable(),
            ]),
        ];
    }

    /**
     * @return list<FieldContract>
     */
    protected function detailFields(): iterable
    {
        return [
            ID::make(),
        ];
    }

    /**
     * @param  QuestionOption  $item
     * @return array<string, string[]|string>
     *
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    protected function rules(mixed $item): array
    {
        return [
            'option' => ['required', 'string', 'max:255'],
            'order' => ['required', 'integer', 'min:1'],
            'question_id' => ['required', 'exists:questions,id'],
        ];
    }

    protected function filters(): iterable
    {
        return [
            BelongsTo::make('Опрос', 'survey', 'title', SurveyResource::class)
                ->nullable()
                ->searchable(),
            BelongsTo::make('Секция', 'section', 'title', SectionResource::class)
                ->nullable()
                ->searchable(),
            BelongsTo::make('Вопрос', 'question', 'question', QuestionResource::class)
                ->nullable()
                ->searchable(),
            Number::make('Номер порядка', 'order'),
        ];
    }

    protected function search(): array
    {
        return [
            'id',
            'option',
            'question.question',
            'survey.title',
            'section.title',
        ];
    }
}
