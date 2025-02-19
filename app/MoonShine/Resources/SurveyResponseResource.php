<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Models\SurveyResponse;
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
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\ID;

/**
 * @extends ModelResource<SurveyResponse>
 */
final class SurveyResponseResource extends ModelResource
{
    protected string $model = SurveyResponse::class;

    protected string $title = 'Ответы на опросы';

    protected array $with = ['survey', 'group'];

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

    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),
            BelongsTo::make('Опрос', 'survey', 'title', SurveyResource::class)->sortable(),
            BelongsTo::make('Группа', 'group', 'title', GroupResource::class)->sortable(),
            Date::make('Начало', 'started_at')->format('d.m.Y H:i:s')->sortable(),
            Date::make('Завершение', 'completed_at')->format('d.m.Y H:i:s')->sortable(),
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
                BelongsTo::make('Опрос', 'survey', 'title', SurveyResource::class)
                    ->required()
                    ->searchable(),
                BelongsTo::make('Группа', 'group', 'title', GroupResource::class)
                    ->required()
                    ->searchable(),
                Date::make('Начало', 'started_at')->withTime()->required(),
                Date::make('Завершение', 'completed_at')->withTime()->required(),
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
     * @param  SurveyResponse  $item
     * @return array<string, string[]|string>
     *
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    protected function rules(mixed $item): array
    {
        return [];
    }

    protected function filters(): iterable
    {
        return [
            BelongsTo::make('Опрос', 'survey', 'title', SurveyResource::class)
                ->nullable()
                ->searchable(),
            BelongsTo::make('Группа', 'group', 'title', GroupResource::class)
                ->nullable()
                ->searchable(),
            Date::make('Начало', 'started_at')->withTime()->nullable(),
            Date::make('Завершение', 'completed_at')->withTime()->nullable(),
        ];
    }

    protected function search(): array
    {
        return [
            'id',
            'survey.title',
            'group.title',
        ];
    }
}
