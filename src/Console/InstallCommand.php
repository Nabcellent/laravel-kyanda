<?php

namespace Nabcellent\Kyanda\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kyanda:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install all Kyanda resources';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->info('Installing Kyanda Scaffolding...');

        $this->info('Publishing Kyanda Provider...');
        $this->callSilent('vendor:publish', ['--tag' => 'kyanda-provider']);

        if (File::exists(config_path('kyanda.php'))) {
            if ($this->shouldOverwriteConfig()) {
                $this->info('Overwriting configuration file...');
                $this->callSilent('vendor:publish', ['--tag' => 'kyanda-config', '--force' => true]);
            } else {
                $this->info('Existing configuration was not overwritten');
            }
        } else {
            $this->info('Publishing Kyanda Configuration...');
            $this->callSilent('vendor:publish', ['--tag' => 'kyanda-config']);
            $this->comment('Published configuration.');
        }

        $this->registerKyandaServiceProvider();

        $this->comment('Kyanda scaffolding installed successfully!');
    }

    private function shouldOverwriteConfig(): bool
    {
        return $this->confirm('Config file already exists. Do you want to overwrite it?', false);
    }

    /**
     * Register the Kyanda service provider in the application configuration file.
     *
     * @return void
     */
    protected function registerKyandaServiceProvider()
    {
        $namespace = Str::replaceLast('\\', '', $this->laravel->getNamespace());

        $appConfig = file_get_contents(config_path('app.php'));

        if (Str::contains($appConfig, $namespace . '\\Providers\\KyandaServiceProvider::class')) {
            return;
        }

        $lineEndingCount = [
            "\r\n" => substr_count($appConfig, "\r\n"),
            "\r"   => substr_count($appConfig, "\r"),
            "\n"   => substr_count($appConfig, "\n"),
        ];

        $eol = array_keys($lineEndingCount, max($lineEndingCount))[0];

        file_put_contents(config_path('app.php'), str_replace(
            "$namespace\\Providers\RouteServiceProvider::class," . $eol,
            "$namespace\\Providers\RouteServiceProvider::class," . $eol . "$namespace\Providers\KyandaServiceProvider::class," . $eol,
            $appConfig
        ));

        file_put_contents(app_path('Providers/KyandaServiceProvider.php'), str_replace(
            "namespace App\Providers;",
            "namespace $namespace\Providers;",
            file_get_contents(app_path('Providers/KyandaServiceProvider.php'))
        ));
    }
}
