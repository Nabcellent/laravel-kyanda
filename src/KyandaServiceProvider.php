<?php

namespace Nabcellent\Kyanda;

use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;
use Nabcellent\Kyanda\Library\Account;
use Nabcellent\Kyanda\Library\BaseClient;
use Nabcellent\Kyanda\Library\Notification;
use Nabcellent\Kyanda\Library\Utility;

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
        $this->app->singleton(BaseClient::class, function ($app) {
            return new BaseClient(new Client(['http_errors' => false]));
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

        $this->registerFacades();
//        $this->registerEvents();
    }

    /**
     * Register facade accessors
     */
    private function registerFacades()
    {
//        IMPORTANT: Facades are with FQDN. Concrete/Implementations are imported, else there could be an error
//        TODO: Should we actually use strings like "account"? for facades that is
        $this->app->bind(
            Facades\Account::class,
            function () {
                return $this->app->make(Account::class);
            }
        );

        $this->app->bind(
            Facades\Utility::class,
            function () {
                return $this->app->make(Utility::class);
            }
        );

        $this->app->bind(
            Facades\Notification::class,
            function () {
                return $this->app->make(Notification::class);
            }
        );
    }

    /**
     * Register events
     */
    private function registerEvents()
    {
//        TODO: Is it necessary to register any events? Does our logic depend on listeners?
//              Or we leave implementation to projects/users?
//        Event::listen(KyandaTransactionSuccessEvent::class, KyandaTransactionSuccess::class);
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
