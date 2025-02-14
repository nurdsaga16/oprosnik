<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Models\Practice;
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
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;

/**
 * @extends ModelResource<Practice>
 */
final class PracticeResource extends ModelResource
{
    protected string $model = Practice::class;

    protected string $title = 'Практики';

    protected string $column = 'name';

    protected int $itemsPerPage = 10;

    protected array $with = ['user', 'subject', 'group'];

    protected bool $cursorPaginate = true;

    protected bool $stickyTable = true;

    protected bool $columnSelection = true;

    protected ?PageType $redirectAfterSave = PageType::INDEX;

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

    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('Название', 'name')->unescape(),
            BelongsTo::make('Предмет', 'subject', 'name', SubjectResource::class)->sortable(),
            BelongsTo::make('Преподаватель', 'user', 'fullname', UserResource::class)->sortable(),
            BelongsTo::make('Группа', 'group', 'name', GroupResource::class)->sortable(),
            Text::make('Активность', 'active', fn ($item) => $item->active ? 'Активный' : 'Неактивный')
                ->badge(fn ($value) => $value === 1 ? 'green' : 'red')->sortable(),
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
                Text::make('Название', 'name'),
                Textarea::make('Описание', 'description'),
                Date::make('Начало', 'start_date'),
                Date::make('Конец', 'end_date'),
                BelongsTo::make('Предмет', 'subject', 'name', SubjectResource::class),
                BelongsTo::make('Преподаватель', 'user', 'fullname', UserResource::class),
                BelongsTo::make('Группа', 'group', 'name', GroupResource::class),
                Switcher::make('Активность', 'active'),
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
            Text::make('Название', 'name')->unescape(),
            Textarea::make('Описание', 'description'),
            Date::make('Начало', 'start_date')->format('d.m.Y'),
            Date::make('Конец', 'end_date')->format('d.m.Y'),
            BelongsTo::make('Предмет', 'subject', 'name', SubjectResource::class),
            BelongsTo::make('Преподаватель', 'user', 'fullname', UserResource::class),
            BelongsTo::make('Группа', 'group', 'name', GroupResource::class),
            Text::make('Активность', 'active', fn ($item) => $item->active ? 'Активный' : 'Неактивный')
                ->badge(fn ($value) => $value === 1 ? 'green' : 'red')->sortable(),
        ];
    }

    /**
     * @param  Practice  $item
     * @return array<string, string[]|string>
     *
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    protected function rules(mixed $item): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_date' => ['required', 'date', 'before_or_equal:end_date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'user_id' => ['required', 'exists:users,id'],
            'group_id' => ['required', 'exists:groups,id'],
            'active' => ['boolean'],
        ];
    }

    protected function filters(): iterable
    {
        return [
            Text::make('Название', 'name'),
            Textarea::make('Описание', 'description'),
            Date::make('Начало', 'start_date'),
            Date::make('Конец', 'end_date'),
            BelongsTo::make('Предмет', 'subject', 'name', SubjectResource::class),
            BelongsTo::make('Преподаватель', 'user', 'fullname', UserResource::class),
            BelongsTo::make('Группа', 'group', 'name', GroupResource::class),
            Switcher::make('Активность', 'active'),
        ];
    }

    protected function search(): array
    {
        return [
            'id',
            'name',
            'description',
            'start_date',
            'end_date',
            'subjects.name',
            'users.name',
            'groups.name',
        ];
    }
}
