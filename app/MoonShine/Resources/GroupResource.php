<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Models\Group;
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
use MoonShine\UI\Fields\Enum;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;

/**
 * @extends ModelResource<Group>
 */
final class GroupResource extends ModelResource
{
    protected string $model = Group::class;

    protected string $title = 'Группы';

    protected string $column = 'title';

    protected int $itemsPerPage = 10;

    protected array $with = ['user', 'specialization', 'department'];

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

    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('Название', 'title'),
            Text::make('Курс', 'course')->sortable(),
            BelongsTo::make('Куратор', 'user', 'fullname', UserResource::class),
            BelongsTo::make('Специальность', 'specialization', 'title', SpecializationResource::class)->sortable(),
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
                Text::make('Название', 'title')->required(),
                Enum::make('Курс', 'course')->options([1 => '1', 2 => '2', 3 => '3'])->required(),
                BelongsTo::make('Куратор', 'user', 'fullname', UserResource::class)
                    ->required()
                    ->searchable(),
                BelongsTo::make('Отделение', 'department', 'title', DepartmentResource::class)
                    ->required()
                    ->searchable(),
                BelongsTo::make('Специальность', 'specialization', 'title', SpecializationResource::class)
                    ->required()
                    ->searchable(),
                Switcher::make('Активный', 'active'),
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
            Text::make('Название', 'title'),
            Text::make('Курс', 'course'),
            BelongsTo::make('Куратор', 'user', 'fullname', UserResource::class),
            BelongsTo::make('Отделение', 'department', 'title', DepartmentResource::class),
            BelongsTo::make('Специальность', 'specialization', 'title', SpecializationResource::class),
            Text::make('Активность', 'active', fn ($item) => $item->active ? 'Активный' : 'Неактивный')
                ->badge(fn ($value) => $value === 1 ? 'green' : 'red')->sortable(),
        ];
    }

    /**
     * @param  Group  $item
     * @return array<string, string[]|string>
     *
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    protected function rules(mixed $item): array
    {
        return [
            'title' => ['required', 'string', 'max:255', 'unique:groups,title,'.($item->id ?? 'null')],
            'course' => ['required', 'integer', 'between:1,3'],
            'active' => ['boolean'],
            'user_id' => ['required', 'exists:user,id'],
            'department_id' => ['required', 'exists:department,id'],
            'specialization_id' => ['required', 'exists:specialization,id'],
        ];
    }

    protected function filters(): iterable
    {
        return [
            Enum::make('Курс', 'course')->options([1 => '1', 2 => '2', 3 => '3'])->nullable(),
            BelongsTo::make('Отделение', 'department', 'title', DepartmentResource::class)
                ->nullable()
                ->searchable(),
            BelongsTo::make('Специальность', 'specialization', 'title', SpecializationResource::class)
                ->nullable()
                ->searchable(),
            BelongsTo::make('Куратор', 'user', 'fullname', UserResource::class)
                ->nullable()
                ->searchable(),
        ];
    }

    protected function search(): array
    {
        return [
            'id',
            'title',
            'user.fullname',
            'department.title',
            'specialization.title',
        ];
    }
}
