<?php

namespace Nabcellent\Src;

use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;
use Nabcellent\Src\Console\InstallCommand;
use Nabcellent\Src\Library\Core;

class KyandaServiceProvider extends ServiceProvider
{
    /**
     * Registers the kyanda service provider.
     *
     * @return void
     */
    public function register()
    {
        $core = new Core(new Client(['http_errors' => false]));
        $this->app->bind(Core::class, function () use ($core) {
            return $core;
        });

        $this->mergeConfigFrom(__DIR__ . '/../config/kyanda.php', 'kyanda');
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
        $this->publishes([__DIR__ . '/../config/kyanda.php' => config_path('kyanda.php'),]);

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
                InstallCommand::class,
            ]);
        }
    }
}
