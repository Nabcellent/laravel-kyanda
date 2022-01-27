<?php

namespace Nabcellent\Kyanda\Tests\Library;

use Illuminate\Support\Facades\Config;
use Nabcellent\Kyanda\Exceptions\KyandaException;
use Nabcellent\Kyanda\Library\Endpoints;
use Nabcellent\Kyanda\Tests\TestCase;

class EndpointsTest extends TestCase
{

    /** @test */
    function get_url_from_valid_endpoint()
    {
        $testUrl = Endpoints::build("test");

        $this->assertStringContainsString(Config::get('kyanda.urls.base'), $testUrl);
    }

    /** @test */
    function throw_error_on_invalid_endpoint()
    {
        $this->expectException(KyandaException::class);

        Endpoints::build("test_invalid");
    }

    /** @test */
    function throw_error_on_unset_base_url()
    {
        Config::set('kyanda.urls.base');

        $this->expectException(KyandaException::class);

        Endpoints::build("test");
    }
}
