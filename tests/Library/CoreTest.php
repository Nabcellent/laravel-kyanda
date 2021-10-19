<?php

namespace Nabcellent\Kyanda\Tests\Library;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Config;
use Nabcellent\Kyanda\Exceptions\KyandaException;
use Nabcellent\Kyanda\Facades\Core;
use Nabcellent\Kyanda\Library\Endpoints;
use Nabcellent\Kyanda\Tests\TestCase;

class CoreTest extends TestCase
{

    /** @test */
    function send_request_throws_exception_with_no_api_key_set()
    {
        $this->expectException(KyandaException::class);

        Config::set('kyanda.api_key', null);

        Core::sendRequest('', []);
    }

    /** @test */
    function send_request_throws_exception_with_no_merchant_id_set()
    {
        $this->expectException(KyandaException::class);

        Config::set('kyanda.api_key', "null");
        Config::set('kyanda.merchant_id', null);

        Core::sendRequest('', []);
    }

    /** @test */
    function send_request_throws_exception_with_incorrect_endpoint()
    {
        $this->expectException(RequestException::class);

        Config::set('kyanda.api_key', 'somethinggoeshere');
        Config::set('kyanda.merchant_id', 'somethinggoeshere');


        Core::sendRequest('', []);
    }

    /** @test */
    function send_request_successfully()
    {
        Config::set('kyanda.api_key', '***REMOVED***');
        Config::set('kyanda.merchant_id', '***REMOVED***');

        $req = Core::sendRequest('https://google.com', []);

        $this->assertInstanceOf(Response::class, $req);
    }


    /** @test */
    function builds_correct_endpoint()
    {
        $endpoint = Endpoints::build('bill');

        $this->assertStringContainsString("/billing/v1/bill/create", $endpoint);
        $this->assertIsNotBool(filter_var($endpoint, FILTER_VALIDATE_URL));
    }
}
