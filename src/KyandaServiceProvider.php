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
        try {
            $this->mergeConfigFrom(__DIR__ . '/../config/kyanda.php', 'kyanda');
        } catch (\TypeError $e) {
            error_log("Could not load config");
        }

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
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');
        $this->publishes([
            __DIR__ . '/../config/kyanda.php' => config_path('kyanda.php'),
        ], 'config');

        $this->registerCommands();
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
