<?php

declare(strict_types=1);

namespace Corepine\Support;

use Corepine\Support\Colors\ColorManager;
use Illuminate\Support\ServiceProvider;

class SupportServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ColorManager::class, static fn (): ColorManager => new ColorManager);
        $this->app->alias(ColorManager::class, 'corepine.colors');
    }
}
