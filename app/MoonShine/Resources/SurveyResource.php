<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Models\Survey;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\Enums\JsEvent;
use MoonShine\Support\Enums\PageType;
use MoonShine\Support\Enums\SortDirection;
use MoonShine\Support\ListOf;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Layout\Flex;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\Enum;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;

/**
 * @extends ModelResource<Survey>
 */
final class SurveyResource extends ModelResource
{
    protected string $model = Survey::class;

    protected string $title = 'Опросы';

    protected string $column = 'title';

    protected int $itemsPerPage = 10;

    protected bool $cursorPaginate = true;

    protected bool $stickyTable = true;

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

    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('Название', 'title'),
            BelongsTo::make('Практика', 'practice', 'title', PracticeResource::class)->sortable(),
            BelongsTo::make('Преподаватель', 'user', 'fullname', PracticeResource::class)->sortable(),
            BelongsTo::make('Группа', 'group', 'title', PracticeResource::class)->sortable(),
            Text::make('Статус', 'status', fn ($item) => match ($item->status) {
                'Активный' => 'Активный',
                'Завершенный' => 'Завершенный',
                'Черновик' => 'Черновик',
                'Архивированный' => 'Архивированный',
            })->badge(fn ($value) => match ($value) {
                'Активный' => 'green',
                'Завершенный' => 'blue',
                'Черновик' => 'yellow',
                'Архивированный' => 'gray',
            })->sortable(),
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
                Number::make('Лимит', 'response_limit')->nullable(),
                Flex::make([
                    Date::make('Начало', 'start_date')->withTime()->required(),
                    Date::make('Конец', 'end_date')->withTime()->required(),
                ]),
                BelongsTo::make('Практика', 'practice', 'title', PracticeResource::class)->required(),
                BelongsTo::make('Преподаватель', 'user', 'fullname', PracticeResource::class)->required(),
                BelongsTo::make('Группа', 'group', 'title', PracticeResource::class)->required(),
                Enum::make('Статус', 'status')
                    ->options([
                        'Активный' => 'Активный',
                        'Завершенный' => 'Завершенный',
                        'Черновик' => 'Черновик',
                        'Архивированный' => 'Архивированный'])->required(),
                Switcher::make('Шаблон', 'template')->required(),
            ]),
        ];
    }

    /**
     * @return list<FieldContract>
     */
    protected function detailFields(): iterable
    {
        return [
            Text::make('Название', 'title'),
            Textarea::make('Описание', 'description'),
            Number::make('Лимит', 'response_limit'),
            Date::make('Начало', 'start_date')->format('d.m.Y H:i:s'),
            Date::make('Конец', 'end_date')->format('d.m.Y H:i:s'),
            BelongsTo::make('Практика', 'practice', 'title', PracticeResource::class),
            BelongsTo::make('Преподаватель', 'user', 'fullname', PracticeResource::class),
            BelongsTo::make('Группа', 'group', 'title', PracticeResource::class),
            Text::make('Статус', 'status', fn ($item) => match ($item->status) {
                'Активный' => 'Активный',
                'Завершенный' => 'Завершенный',
                'Черновик' => 'Черновик',
                'Архивированный' => 'Архивированный',
            })->badge(fn ($value) => match ($value) {
                'Активный' => 'green',
                'Завершенный' => 'blue',
                'Черновик' => 'yellow',
                'Архивированный' => 'gray',
            }),
            Text::make('Шаблон', 'template', fn ($item) => $item->active ? 'Да' : 'Нет')
                ->badge(fn ($value) => $value === 1 ? 'green' : 'red')->sortable(),
        ];
    }

    /**
     * @param  Survey  $item
     * @return array<string, string[]|string>
     *
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    protected function rules(mixed $item): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'response_limit' => ['nullable', 'integer', 'min:1'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'practice_id' => ['required', 'exists:practices,id'],
            'user_id' => ['required', 'exists:users,id'],
            'group_id' => ['required', 'exists:groups,id'],
            'status' => ['required', 'in:Активный,Завершенный,Черновик,Архивированный'],
            'template' => ['boolean'],
        ];
    }

    protected function filters(): iterable
    {
        return [
            Number::make('Лимит', 'response_limit')->nullable(),
            Date::make('Начало', 'start_date')->withTime()->nullable(),
            Date::make('Конец', 'end_date')->withTime()->nullable(),
            BelongsTo::make('Практика', 'practice', 'title', PracticeResource::class)->nullable(),
            BelongsTo::make('Преподаватель', 'user', 'fullname', PracticeResource::class)->nullable(),
            BelongsTo::make('Группа', 'group', 'title', PracticeResource::class)->nullable(),
            Enum::make('Статус', 'status')->options([1 => 'Активный', 2 => 'Завершенный', 3 => 'Черновик', 4 => 'Архивированный'])->nullable(),
            Switcher::make('Шаблон', 'template')->nullable(),
        ];
    }

    protected function search(): array
    {
        return [
            'id',
            'title',
            'description',
            'start_date',
            'end_date',
            'practice.title',
            'user.fullname',
            'group.title',
            'status',
        ];
    }
}
