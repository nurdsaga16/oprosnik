<?php

declare(strict_types=1);

namespace App\Providers;

use App\MoonShine\Resources\DepartmentResource;
use App\MoonShine\Resources\GroupResource;
use App\MoonShine\Resources\MoonShineUserResource;
use App\MoonShine\Resources\MoonShineUserRoleResource;
use App\MoonShine\Resources\PracticeResource;
use App\MoonShine\Resources\QuestionOptionResource;
use App\MoonShine\Resources\QuestionResource;
use App\MoonShine\Resources\SectionResource;
use App\MoonShine\Resources\SpecializationResource;
use App\MoonShine\Resources\SubjectResource;
use App\MoonShine\Resources\SubjectUserResource;
use App\MoonShine\Resources\SurveyResource;
use App\MoonShine\Resources\UserResource;
use Illuminate\Support\ServiceProvider;
use MoonShine\Contracts\Core\DependencyInjection\ConfiguratorContract;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Laravel\DependencyInjection\MoonShine;
use MoonShine\Laravel\DependencyInjection\MoonShineConfigurator;
use App\MoonShine\Resources\SurveyResponseResource;

final class MoonShineServiceProvider extends ServiceProvider
{
    /**
     * @param  MoonShine  $core
     * @param  MoonShineConfigurator  $config
     */
    public function boot(CoreContract $core, ConfiguratorContract $config): void
    {
        // $config->authEnable();

        $core
            ->resources([
                MoonShineUserResource::class,
                MoonShineUserRoleResource::class,
                DepartmentResource::class,
                SpecializationResource::class,
                UserResource::class,
                GroupResource::class,
                SubjectResource::class,
                SubjectUserResource::class,
                PracticeResource::class,
                SurveyResource::class,
                QuestionResource::class,
                SectionResource::class,
                QuestionOptionResource::class,
                SurveyResponseResource::class,
            ])
            ->pages([
                ...$config->getPages(),
            ]);

    }
}
