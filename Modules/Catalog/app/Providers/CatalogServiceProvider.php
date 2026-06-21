<?php

namespace Modules\Catalog\Providers;

use App\Contracts\CatalogInterface;
use Livewire\Livewire;
use Modules\Catalog\Services\CatalogService;
use Nwidart\Modules\Support\ModuleServiceProvider;

class CatalogServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'Catalog';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'catalog';

    /**
     * Command classes to register.
     *
     * @var string[]
     */
    // protected array $commands = [];

    /**
     * Provider classes to register.
     *
     * @var string[]
     */
    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    /**
     * Define module schedules.
     *
     * @param  $schedule
     */
    // protected function configureSchedules(Schedule $schedule): void
    // {
    //     $schedule->command('inspire')->hourly();
    // }

    public function register(): void
    {
        parent::register();

        $this->app->singleton(
            CatalogInterface::class,
            CatalogService::class,
        );
    }

    public function boot(): void
    {
        $this->loadViewsFrom(module_path('Catalog', 'resources/views'), 'catalog');

        Livewire::addNamespace(
            namespace: 'catalog',
            viewPath: module_path('Catalog', 'resources/views/components'),
        );
    }
}
