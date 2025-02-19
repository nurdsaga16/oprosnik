<?php

declare(strict_types=1);

namespace App\MoonShine\Pages;

use App\Models\Department;
use App\Models\Group;
use App\Models\Practice;
use App\Models\Specialization;
use App\Models\Subject;
use App\Models\Survey;
use App\Models\User;
use MoonShine\Apexcharts\Components\DonutChartMetric;
use MoonShine\Apexcharts\Components\LineChartMetric;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Laravel\Pages\Page;
use MoonShine\UI\Components\Layout\Column;
use MoonShine\UI\Components\Layout\Grid;
use MoonShine\UI\Components\Layout\LineBreak;
use MoonShine\UI\Components\Metrics\Wrapped\ValueMetric;

final class Dashboard extends Page
{
    /**
     * @return array<string, string>
     */
    public function getBreadcrumbs(): array
    {
        return [
            '#' => $this->getTitle(),
        ];
    }

    public function getTitle(): string
    {
        return $this->title ?: 'Панель управления';
    }

    /**
     * @return list<ComponentContract>
     */
    protected function components(): iterable
    {
        return [

            LineBreak::make(),

            Grid::make([
                Column::make([
                    ValueMetric::make('Отделения')
                        ->value(Department::query()->count())
                        ->icon('squares-2x2'),
                ])->columnSpan(3),

                Column::make([
                    ValueMetric::make('Специальности')
                        ->value(Specialization::query()->count())
                        ->icon('briefcase'),
                ])->columnSpan(3),

                Column::make([
                    ValueMetric::make('Преподаватели')
                        ->value(User::query()->count())
                        ->icon('user-circle'),
                ])->columnSpan(3),

                Column::make([
                    ValueMetric::make('Группы')
                        ->value(Group::query()->count())
                        ->icon('user-group'),
                ])->columnSpan(3),

                Column::make([
                    ValueMetric::make('Предметы')
                        ->value(Subject::query()->count())
                        ->icon('book-open'),
                ])->columnSpan(4),

                Column::make([
                    ValueMetric::make('Практики')
                        ->value(Practice::query()->count())
                        ->icon('presentation-chart-line'),
                ])->columnSpan(4),

                Column::make([
                    ValueMetric::make('Опросы')
                        ->value(Survey::query()->count())
                        ->icon('chart-bar-square'),
                ])->columnSpan(4),

                //                Column::make([
                //                    DonutChartMetric::make('Подписчики')
                //                        ->columnSpan(6)
                //                        ->values(['CutCode' => 10000, 'Apple' => 9999]),
                //                ])->columnSpan(6),
                //
                //                Column::make([
                //                    LineChartMetric::make('Заказы')
                //                        ->line([
                //                            'Выручка 1' => [
                //                                now()->format('Y-m-d') => 100,
                //                                now()->addDay()->format('Y-m-d') => 200,
                //                            ],
                //                        ])
                //                        ->line([
                //                            'Выручка 2' => [
                //                                now()->format('Y-m-d') => 300,
                //                                now()->addDay()->format('Y-m-d') => 400,
                //                            ],
                //                        ], '#EC4176'),
                //                ])->columnSpan(6),
            ]),
        ];
    }
}
