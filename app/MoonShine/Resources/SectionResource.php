<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Models\Section;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Enums\Action;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\Enums\JsEvent;
use MoonShine\Support\Enums\SortDirection;
use MoonShine\Support\ListOf;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;

/**
 * @extends ModelResource<Section>
 */
final class SectionResource extends ModelResource
{
    protected string $model = Section::class;

    protected string $title = 'Секции';

    protected array $with = ['survey'];

    protected bool $createInModal = true;

    protected int $itemsPerPage = 10;

    protected bool $cursorPaginate = true;

    protected bool $columnSelection = true;

    protected SortDirection $sortDirection = SortDirection::ASC;

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
            Text::make('Название', 'title'),
            Textarea::make('Описание', 'description'),
            Number::make('Номер порядка', 'order')->sortable(),
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
                Text::make('Название', 'title')->required(),
                Textarea::make('Описание', 'description')->nullable(),
                Number::make('Номер порядка', 'order')->required(),
                BelongsTo::make('Опрос', 'survey', 'title', SurveyResource::class)
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
     * @param  Section  $item
     * @return array<string, string[]|string>
     *
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    protected function rules(mixed $item): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'order' => ['required', 'integer', 'min:1'],
            'survey_id' => ['required', 'exists:surveys,id'],
        ];
    }

    protected function filters(): iterable
    {
        return [
            BelongsTo::make('Опрос', 'survey', 'title', SurveyResource::class)
                ->nullable()
                ->searchable(),
            Number::make('Номер порядка', 'order')->nullable(),
        ];
    }

    protected function search(): array
    {
        return [
            'id',
            'title',
            'description',
        ];
    }
}
