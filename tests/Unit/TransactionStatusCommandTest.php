<?php

namespace Nabcellent\Kyanda\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Nabcellent\Kyanda\Tests\TestCase;

class TransactionStatusCommandTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    function the_command_queries_unresolved_requests()
    {
        $this->artisan('kyanda:query_status')
            ->expectsOutput('Nothing to query... all transactions seem to be ok.')
            ->assertExitCode(0);
    }
}
