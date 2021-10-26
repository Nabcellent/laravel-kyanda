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

        $this->comment('Kyanda scaffolding installed successfully!');
    }

    private function shouldOverwriteConfig(): bool
    {
        return $this->confirm('Config file already exists. Do you want to overwrite it?', false);
    }
}
