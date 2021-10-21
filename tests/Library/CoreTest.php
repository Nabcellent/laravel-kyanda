<?php

namespace Nabcellent\Kyanda\Tests\Library;

use ErrorException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Config;
use Nabcellent\Kyanda\Exceptions\KyandaException;
use Nabcellent\Kyanda\Facades\Core;
use Nabcellent\Kyanda\Library\Providers;
use Nabcellent\Kyanda\Library\Endpoints;
use Nabcellent\Kyanda\Tests\TestCase;

class CoreTest extends TestCase
{

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

        Config::set('kyanda.api_key', "null");
        Config::set('kyanda.merchant_id');

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

        $req = Core::sendRequest('https://github.com', []);

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
    function request_throws_error_on_non_existing_endpoint()
    {
//        TODO: Change endpoint class to ensure proper exception is thrown
        $this->expectException(ErrorException::class);

        Config::set('kyanda.api_key', 'somethinggoeshere');
        Config::set('kyanda.merchant_id', 'somethinggoeshere');

        Core::request('https://github.com', []);
    }


    /** @test */
    function request_throws_error_on_existing_endpoint()
    {
//        TODO: Change endpoint class to ensure proper exception is thrown
        $this->expectException(ErrorException::class);

        Config::set('kyanda.api_key', 'somethinggoeshere');
        Config::set('kyanda.merchant_id', 'somethinggoeshere');

        $req = Core::request('test', []);

        var_dump($req);
    }


    /** @test */
    function gets_correct_telco_channels_from_phone()
    {
        $testArr = [
            700000000 => Providers::SAFARICOM,
            748000000 => Providers::SAFARICOM,
            110000000 => Providers::SAFARICOM,
            730000000 => Providers::AIRTEL,
            762000000 => Providers::AIRTEL,
            106000000 => Providers::AIRTEL,
            779000000 => Providers::TELKOM,
            764000000 => Providers::EQUITEL,
            747000000 => Providers::FAIBA,
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

        Core::getTelcoFromPhone(108000000);
    }

    /** @test */
    function formats_phone_numbers_correctly()
    {
        $testArr = [
            "+254700000000"   => "0700000000",
            "254750000000"    => "0750000000",
            "0730000000"      => "0730000000",
            "762000000"       => "0762000000",
            "+254100000000"   => "0100000000",
            "0130000000"      => "0130000000",
            "162000000"       => "0162000000",
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
            "254256000000"    => "0256000000",  //Throws exception
            "-0254110000000"  => "0110000000",  //Throws exception
            "-0251110000000"  => "0111000000",  //Throws exception
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
