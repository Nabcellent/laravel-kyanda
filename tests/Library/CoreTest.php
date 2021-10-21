<?php

namespace Nabcellent\Kyanda\Tests\Library;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Config;
use Nabcellent\Kyanda\Exceptions\KyandaException;
use Nabcellent\Kyanda\Facades\Core;
use Nabcellent\Kyanda\Library\Providers;
use Nabcellent\Kyanda\Library\Endpoints;
use Nabcellent\Kyanda\Tests\MockServerTestCase;

class CoreTest extends MockServerTestCase
{
//    TODO: Refactor Class to use MockServerTestCase

    /** @test */
    function send_request_throws_exception_with_no_api_key_set()
    {
        $this->expectException(KyandaException::class);

        Config::set('kyanda.api_key');

        Core::sendRequest('', []);
    }

    /** @test */
    function send_request_throws_exception_with_no_merchant_id_set()
    {
        $this->expectException(KyandaException::class);

        Config::set('kyanda.merchant_id');

        Core::sendRequest('', []);
    }

    /** @test */
    function send_request_throws_exception_with_incorrect_endpoint()
    {
        $this->expectException(RequestException::class);

        Core::sendRequest('', []);
    }

    /** @test */
    function send_request_successfully()
    {
        $this->mock->append(
            new Response(200, ['Content_type' => 'application/json'],
                json_encode($this->mockResponses['request_success'])));

//        test endpoint does not work, it creates '//' url which can't be parsed. fix?
        $req = (new \Nabcellent\Kyanda\Library\Core($this->_client))->request('bill', []);

        $this->assertIsArray($req);
        $this->assertEquals(0000, $req['status_code']);

    }


    /** @test */
    function builds_correct_endpoint()
    {
        Config::set('kyanda.urls.base', 'http://localhost');
        $endpoint = Endpoints::build('bill');

        $this->assertStringContainsString("/billing/v1/bill/create", $endpoint);
        $this->assertIsNotBool(filter_var($endpoint, FILTER_VALIDATE_URL));
    }

    /** @test */
    function request_throws_error_on_non_existing_endpoint()
    {
        $this->expectException(KyandaException::class);

        Core::request('https://github.com', []);
    }


    /** @test */
    function successful_request_throws_error_when_server_is_not_200()
    {
        //    TODO: Confirm what this test below does...!

        $this->mock->append(
            new Response(301, ['Content_type' => 'application/json'],
                json_encode($this->mockResponses['request_failed'])));

        Config::set('kyanda.urls.base', 'http://localhost');

        $this->expectException(KyandaException::class);

        (new \Nabcellent\Kyanda\Library\Core($this->_client))->request('test', []);
    }

    /** @test */
    function request_throws_error_when_server_is_500()
    {
        //    TODO: Confirm what this test below does...!

        $this->mock->append(
            new Response(500, ['Content_type' => 'application/json'],
                null));

        Config::set('kyanda.urls.base', 'http://localhost');

        $this->expectException(KyandaException::class);

        (new \Nabcellent\Kyanda\Library\Core($this->_client))->request('test', []);
    }


    /** @test */
    function gets_correct_telco_channels_from_phone()
    {
        $testArr = [
            "700000000" => Providers::SAFARICOM,
            "748000000" => Providers::SAFARICOM,
            "110000000" => Providers::SAFARICOM,
            "730000000" => Providers::AIRTEL,
            "762000000" => Providers::AIRTEL,
            "106000000" => Providers::AIRTEL,
            "779000000" => Providers::TELKOM,
            "764000000" => Providers::EQUITEL,
            "747000000" => Providers::FAIBA,
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

        Core::getTelcoFromPhone("108000000");
    }

    /** @test */
    function formats_phone_numbers_correctly()
    {
        $testArr = [
            "+254700000000" => "0700000000",
            "254750000000" => "0750000000",
            "0730000000" => "0730000000",
            "762000000" => "0762000000",
            "+254100000000" => "0100000000",
            "0130000000" => "0130000000",
            "162000000" => "0162000000",
        ];

        foreach ($testArr as $key => $value) {
            $no = Core::formatPhoneNumber($key);

            $this->assertEquals($value, $no);
        }
    }

    /** @test */
    function format_phone_throws_error_on_invalid_number()
    {
        $testArr = [
            "254256000000" => "0256000000",  //Throws exception
            "-0254110000000" => "0110000000",  //Throws exception
            "-0251110000000" => "0111000000",  //Throws exception
        ];

        $errors = [];

        foreach ($testArr as $key => $value) {
            try {
                Core::formatPhoneNumber($key);
            } catch (KyandaException $e) {
                array_push($errors, $e);
            }
        }

        $this->assertCount(3, $errors);
    }
}
