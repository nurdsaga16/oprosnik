<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Models\Specialization;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Enums\Action;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Support\Enums\PageType;
use MoonShine\Support\Enums\SortDirection;
use MoonShine\Support\ListOf;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;

/**
 * @extends ModelResource<Specialization>
 */
final class SpecializationResource extends ModelResource
{
    protected string $model = Specialization::class;

    protected string $title = 'Специальности';

    protected SortDirection $sortDirection = SortDirection::ASC;

    protected ?PageType $redirectAfterSave = PageType::INDEX;

    /**
     * @return list<FieldContract>
     */
    protected function activeActions(): ListOf
    {
        return parent::activeActions()->except(Action::VIEW);
    }

    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('Название', 'name'),
            BelongsTo::make('Отделение', 'department', 'name', DepartmentResource::class)->sortable(),
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
                BelongsTo::make('Отделение', 'department', 'name', DepartmentResource::class),
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
            Text::make('Название', 'name'),
            BelongsTo::make('Отделение', 'department', 'name', DepartmentResource::class),
        ];
    }

    /**
     * @param  Specialization  $item
     * @return array<string, string[]|string>
     *
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    protected function rules(mixed $item): array
    {
        return [
        ];
    }

    protected function filters(): iterable
    {
        return [
            BelongsTo::make('Отделение', 'department', 'name', DepartmentResource::class),
            Text::make('Название', 'name'),
        ];
    }

    protected function search(): array
    {
        return [
            'id',
            'name',
            'department.name',
        ];
    }
}
