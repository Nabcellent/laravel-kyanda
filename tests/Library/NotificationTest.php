<?php

namespace Nabcellent\Kyanda\Tests\Library;

use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Config;
use Nabcellent\Kyanda\Exceptions\KyandaException;
use Nabcellent\Kyanda\Facades\Notification;
use Nabcellent\Kyanda\Tests\MockServerTestCase;


class NotificationTest extends MockServerTestCase
{

    /** @test */
    function register_callback()
    {
        $this->mock->append(
            new Response(200, ['Content_type' => 'application/json'],
                json_encode($this->mockResponses['request_success'])));

        $res = (new \Nabcellent\Kyanda\Library\Notification($this->_client))->registerCallbackURL("/test");

        $this->assertIsArray($res);
        $this->assertEquals(0000, $res['status_code']);
    }


    /** @test */
    function fails_when_url_is_not_provided_or_set()
    {
//        This is to enable code coverage for Notification facade as well
        $this->expectException(KyandaException::class);

        Config::set('kyanda.urls.callback');

        Notification::registerCallbackURL();
    }
}