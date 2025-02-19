<?php

declare(strict_types=1);

namespace App\MoonShine\Layouts;

use App\MoonShine\Resources\DepartmentResource;
use App\MoonShine\Resources\GroupResource;
use App\MoonShine\Resources\PracticeResource;
use App\MoonShine\Resources\QuestionOptionResource;
use App\MoonShine\Resources\QuestionResource;
use App\MoonShine\Resources\SectionResource;
use App\MoonShine\Resources\SpecializationResource;
use App\MoonShine\Resources\SubjectResource;
use App\MoonShine\Resources\SubjectUserResource;
use App\MoonShine\Resources\SurveyResource;
use App\MoonShine\Resources\SurveyResponseResource;
use App\MoonShine\Resources\UserResource;
use MoonShine\ColorManager\ColorManager;
use MoonShine\Contracts\ColorManager\ColorManagerContract;
use MoonShine\Laravel\Layouts\AppLayout;
use MoonShine\MenuManager\MenuGroup;
use MoonShine\MenuManager\MenuItem;
use MoonShine\UI\Components\Layout\Layout;

final class MoonShineLayout extends AppLayout
{
    protected function assets(): array
    {
        return [
            ...parent::assets(),
        ];
    }

    protected function menu(): array
    {
        return [
            ...parent::menu(),
            MenuGroup::make('Колледж', [
                MenuItem::make('Отделения', DepartmentResource::class, 'squares-2x2'),
                MenuItem::make('Специальности', SpecializationResource::class, 'briefcase'),
                MenuItem::make('Преподаватели', UserResource::class, 'user-circle'),
                MenuItem::make('Группы', GroupResource::class, 'user-group'),
            ], 'academic-cap'),
            MenuGroup::make('Обучение', [
                MenuItem::make('Предметы', SubjectResource::class, 'book-open'),
                MenuItem::make('Предмет - Преподаватель', SubjectUserResource::class, 'link'),
                MenuItem::make('Практики', PracticeResource::class, 'presentation-chart-line'),
            ], 'code-bracket-square'),
            MenuGroup::make('Опросник', [
                MenuItem::make('Опросы', SurveyResource::class, 'chart-bar-square'),
                MenuItem::make('Секции', SectionResource::class, 'document-duplicate'),
                MenuItem::make('Вопросы', QuestionResource::class, 'question-mark-circle'),
                MenuItem::make('Варианты ответов', QuestionOptionResource::class, 'check-circle'),
            ], 'pencil-square'),
            MenuGroup::make('Ответы', [
                MenuItem::make('Ответы на опросы', SurveyResponseResource::class, 'clipboard-document-check'),
            ], 'paper-airplane'),
        ];
    }

    /**
     * @param  ColorManager  $colorManager
     */
    protected function colors(ColorManagerContract $colorManager): void
    {
        parent::colors($colorManager);

        //        $colorManager->primary('#00000');
    }

    public function build(): Layout
    {
        return parent::build();
    }
}
