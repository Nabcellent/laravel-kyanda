<?php

namespace Nabcellent\Kyanda;

use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;
use Nabcellent\Kyanda\Library\Core;

class KyandaServiceProvider extends ServiceProvider
{
    /**
     * Registers the kyanda service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/kyanda.php', 'kyanda');

//        TODO: Change this to bind for a stateless sort of lib
        $this->app->singleton(Core::class, function ($app) {
            return new Core(new Client(['http_errors' => false]));
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/Http/routes.php');
        $this->publishes([
            __DIR__ . '/../config/kyanda.php' => config_path('kyanda.php'),
        ], 'kyanda-config');

        $this->registerMigrations();

        $this->registerCommands();
    }

    /**
     * Register the package's migrations.
     *
     * @return void
     */
    protected function registerMigrations()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    /**
     * Register the package's commands.
     *
     * @return void
     */
    protected function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\InstallCommand::class,
            ]);
        }
    }
}
