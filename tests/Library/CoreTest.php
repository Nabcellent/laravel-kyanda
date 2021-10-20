<?php

namespace Nabcellent\Kyanda\Tests\Library;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Config;
use Nabcellent\Kyanda\Exceptions\KyandaException;
use Nabcellent\Kyanda\Facades\Core;
use Nabcellent\Kyanda\Library\Channels;
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
        Config::set('kyanda.api_key', 'somethinggoeshere');
        Config::set('kyanda.merchant_id', 'somethinggoeshere');

        $req = Core::sendRequest('http://github.com', []);

        $this->assertInstanceOf(Response::class, $req);
    }


    /** @test */
    function builds_correct_endpoint()
    {
        $endpoint = Endpoints::build('bill');

        $this->assertStringContainsString("/billing/v1/bill/create", $endpoint);
        $this->assertIsNotBool(filter_var($endpoint, FILTER_VALIDATE_URL));
    }

    /** @test */
    function gets_correct_telco_channels_from_phone()
    {
        $testArr = [
            700000000 => Channels::SAFARICOM,
            748000000 => Channels::SAFARICOM,
            110000000 => Channels::SAFARICOM,
            730000000 => Channels::AIRTEL,
            762000000 => Channels::AIRTEL,
            106000000 => Channels::AIRTEL,
            779000000 => Channels::TELKOM,
            764000000 => Channels::EQUITEL,
            747000000 => Channels::FAIBA,
        ];

        foreach ($testArr as $key => $value) {
            $no = Core::getTelcoFromPhone($key);

            $this->assertEquals($value, $no);
        }

    }


    /** @test */
    function throws_error_on_invalid_phone()
    {
        $this->expectException(KyandaException::class);

//            Core::getTelcoFromPhone(12839);
        Core::getTelcoFromPhone(108000000);

    }

//    TODO: Add tests for formatting phone number : formatPhoneNumber

}
