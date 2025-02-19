<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Models\Question;
use Illuminate\Contracts\Database\Eloquent\Builder;
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
use MoonShine\UI\Components\CardsBuilder;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\Enum;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;

/**
 * @extends ModelResource<Question>
 */
final class QuestionResource extends ModelResource
{
    protected string $model = Question::class;

    protected string $title = 'Вопросы';

    protected array $with = ['survey', 'section'];

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

    public function getListEventName(?string $name = null, array $params = []): string
    {
        $name ??= $this->getListComponentName();

        return AlpineJs::event(JsEvent::CARDS_UPDATED, $name, $params);
    }

    /**
     * @throws \Throwable
     */
    public function modifyListComponent(ComponentContract $component): ComponentContract
    {
        return CardsBuilder::make($this->getItems(), $this->getIndexFields())
            ->cast($this->getCaster())
            ->name($this->getListComponentName())
            ->async()
            ->overlay()
            ->title('question') // Укажите поле для заголовка карточки
            ->subtitle(fn ($item) => match ($item->question_type) {
                'Текст' => 'Тип: Текст',
                'Множественный выбор' => 'Тип: Множественный выбор',
                'Оценка' => 'Тип: Оценка',
                default => 'Тип: Неизвестный',
            }) // Укажите поле для подзаголовка карточки
            ->url(fn ($item) => $this->getFormPageUrl($item->getKey())) // Измените на правильный ключ
            ->buttons($this->getIndexButtons());
    }

    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('Вопрос', 'question'),
            BelongsTo::make('Опрос', 'survey', 'title', SurveyResource::class),
            Text::make('Тип вопроса', 'question_type', fn ($item) => match ($item->question_type) {
                'Текст' => 'Текст',
                'Множественный выбор' => 'Множественный выбор',
                'Оценка' => 'Оценка',
            })->badge('purple')->sortable(),
            Number::make('Номер порядка', 'order')->sortable(),
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
                Text::make('Вопрос', 'question')->required(),
                Textarea::make('Описание', 'description')->nullable(),
                Enum::make('Тип вопроса', 'question_type')
                    ->options([
                        'Текст' => 'Текст',
                        'Множественный выбор' => 'Множественный выбор',
                        'Оценка' => 'Оценка'])->required(),
                Number::make('Номер порядка', 'order')->required(),
                BelongsTo::make('Опрос', 'survey', 'title', SurveyResource::class)
                    ->required()
                    ->searchable(),
                BelongsTo::make('Секция', 'section', 'title', SectionResource::class)
                    ->reactive()
                    ->creatable()
                    ->valuesQuery(static fn (Builder $q) => $q->select(['id', 'title']))
                    ->searchable()
                    ->nullable(),
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
            Text::make('Вопрос', 'question'),
            Textarea::make('Описание', 'description'),
            Text::make('Тип вопроса', 'question_type', fn ($item) => match ($item->question_type) {
                'Текст' => 'Текст',
                'Множественный выбор' => 'Множественный выбор',
                'Оценка' => 'Оценка',
            })->badge('purple'),
            Number::make('Номер порядка', 'order'),
            BelongsTo::make('Опрос', 'survey', 'title', SurveyResource::class),
            BelongsTo::make('Секция', 'section', 'title', SectionResource::class),
        ];
    }

    /**
     * @param  Question  $item
     * @return array<string, string[]|string>
     *
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    protected function rules(mixed $item): array
    {
        return [
            'question' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'question_type' => ['required', 'in:Текст,Множественный выбор,Оценка'],
            'order' => ['required', 'integer', 'min:1'],
            'survey_id' => ['required', 'exists:surveys,id'],
            'section_id' => ['nullable', 'exists:sections,id'],
        ];
    }

    protected function filters(): iterable
    {
        return [
            BelongsTo::make('Опрос', 'survey', 'title', SurveyResource::class)
                ->nullable()
                ->searchable(),
            BelongsTo::make('Секция', 'section', 'title', SurveyResource::class)
                ->nullable()
                ->searchable(),
            Enum::make('Тип вопроса', 'question_type')
                ->options([
                    'Текст' => 'Текст',
                    'Множественный выбор' => 'Множественный выбор',
                    'Оценка' => 'Оценка'])->nullable(),
            Number::make('Номер порядка', 'order'),
        ];
    }

    protected function search(): array
    {
        return [
            'id',
            'title',
            'description',
            'question_type',
            'survey.title',
            'section.title',
        ];
    }
}
