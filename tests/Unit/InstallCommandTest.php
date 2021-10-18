<?php

namespace Nabcellent\Tests\Unit;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Nabcellent\Tests\TestCase;

class InstallCommandTest extends TestCase
{
    /** @test */
    function the_install_command_copies_the_configuration()
    {
        // Make sure we're starting from a clean state
        if (File::exists(config_path('kyanda.php'))) {
            unlink(config_path('kyanda.php'));
        }

        $this->assertFalse(File::exists(config_path('kyanda.php')));

        Artisan::call('kyanda:install');

        $this->assertTrue(File::exists(config_path('kyanda.php')));
    }

    // 'tests/Unit/InstallCommand.php'

    /** @test */
    public function when_a_config_file_is_present_users_can_choose_to_not_overwrite_it()
    {
        // Given we already have an existing config file
        File::put(config_path('kyanda.php'), 'test contents');
        $this->assertTrue(File::exists(config_path('kyanda.php')));

        // When we run the installation command
        $command = $this->artisan('kyanda:install');

        // We expect a warning that our configuration file exists
        $command->expectsConfirmation('Config file already exists. Do you want to overwrite it?');

        // When answered with "no", We should see a message that our file was not overwritten
        $command->expectsOutput('Existing configuration was not overwritten');

        // Assert that the original contents of the config file remain
        $this->assertEquals('test contents', file_get_contents(config_path('kyanda.php')));

        // Clean up
        unlink(config_path('kyanda.php'));
    }

    /** @test */
    public function when_a_config_file_is_present_users_can_choose_to_do_overwrite_it()
    {
        // Given we already have an existing config file
        File::put(config_path('kyanda.php'), 'test contents');
        $this->assertTrue(File::exists(config_path('kyanda.php')));

        // When we run the installation command
        $command = $this->artisan('kyanda:install');

        // We expect a warning that our configuration file exists
        $command->expectsConfirmation('Config file already exists. Do you want to overwrite it?', 'yes');

        // When answered with "yes", execute the command to force override
        $command->execute();

        $command->expectsOutput('Overwriting configuration file...');

        // Assert that the original contents are overwritten
        $this->assertEquals(file_get_contents(__DIR__ . '/../Config/kyanda.php'), file_get_contents(config_path('kyanda.php')));

        // Clean up
        unlink(config_path('kyanda.php'));
    }
}
