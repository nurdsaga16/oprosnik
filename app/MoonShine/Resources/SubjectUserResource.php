<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Models\SubjectUser;
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
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;

/**
 * @extends ModelResource<SubjectUser>
 */
final class SubjectUserResource extends ModelResource
{
    protected string $model = SubjectUser::class;

    protected string $title = 'Предмет - Преподаватель';

    protected string $sortColumn = 'active';

    protected int $itemsPerPage = 10;

    protected array $with = ['subject', 'user'];

    protected bool $cursorPaginate = true;

    protected bool $stickyTable = true;

    protected ?PageType $redirectAfterSave = PageType::INDEX;

    protected SortDirection $sortDirection = SortDirection::DESC;

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
            BelongsTo::make('Предмет', 'subject', 'name', SubjectResource::class)->sortable(),
            BelongsTo::make('Преподаватель', 'user', 'fullname', UserResource::class)->sortable(),
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
                BelongsTo::make('Предмет', 'subject', 'name', SubjectResource::class),
                BelongsTo::make('Преподаватель', 'user', 'fullname', UserResource::class),
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
        ];
    }

    /**
     * @param  SubjectUser  $item
     * @return array<string, string[]|string>
     *
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    protected function rules(mixed $item): array
    {
        return [
            'subject_id' => ['required', 'exists:subjects,id'],
            'user_id' => ['required', 'exists:users,id'],
            'active' => ['boolean'],
        ];
    }

    protected function filters(): iterable
    {
        return [
            BelongsTo::make('Предмет', 'subject', 'name', SubjectResource::class),
            BelongsTo::make('Преподаватель', 'user', 'fullname', UserResource::class),
            Switcher::make('Активность', 'active'),
        ];
    }

    protected function search(): array
    {
        return [
            'id',
            'subject.name',
            'user.name',
        ];
    }
}
