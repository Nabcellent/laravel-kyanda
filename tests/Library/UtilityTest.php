<?php

namespace Nabcellent\Kyanda\Tests\Library;

use GuzzleHttp\Psr7\Response;
use Nabcellent\Kyanda\Exceptions\KyandaException;
use Nabcellent\Kyanda\Library\Providers;
use Nabcellent\Kyanda\Library\Utility;
use Nabcellent\Kyanda\Models\KyandaRequest;
use Nabcellent\Kyanda\Tests\MockServerTestCase;


class UtilityTest extends MockServerTestCase
{
    /** @test */
    function airtime_purchase_is_successful()
    {
        $this->mock->append(
            new Response(200, ['Content_type' => 'application/json'],
                json_encode($this->mockResponses['request_success'])));

        $res = (new Utility($this->_client))->airtimePurchase(765432100, 10, false);

        $this->assertIsArray($res);
        $this->assertEquals('0000', $res['status_code']);
    }

    /** @test */
    function airtime_purchase_request_is_saved_to_db()
    {
        $this->mock->append(
            new Response(200, ['Content_type' => 'application/json'],
                json_encode($this->mockResponses['request_success'])));

        $res = (new Utility($this->_client))->airtimePurchase("700000000", 10);

        $this->assertIsArray($res);
    }

    /** @test */
    function airtime_purchase_fails_on_invalid_phone()
    {
        $this->expectException(KyandaException::class);

//        Use facade class for testing coverage
        \Nabcellent\Kyanda\Facades\Utility::airtimePurchase("123765432100", 10);
    }

    /** @test */
    function airtime_purchase_fails_on_invalid_phone_2()
    {
        $this->expectException(KyandaException::class);

        (new Utility($this->_client))->airtimePurchase("108000000", 10);
    }

    /** @test */
    function airtime_purchase_fails_when_unable_to_save()
    {
        $this->mock->append(
            new Response(200, ['Content_type' => 'application/json'],
                json_encode($this->mockResponses['request_failed'])));

        $this->expectException(KyandaException::class);

        (new Utility($this->_client))->airtimePurchase("700000000", 10);
    }


###################################################################################
## Bill Payment Tests
###################################################################################

    /** @test */
    function bill_payment_is_successful()
    {
        $this->mock->append(
            new Response(200, ['Content_type' => 'application/json'],
                json_encode($this->mockResponses['request_success'])));

        $res = (new Utility($this->_client))->billPayment(765432100, 10, Providers::DSTV, 765432100, false);

        $this->assertIsArray($res);
        $this->assertEquals('0000', $res['status_code']);
    }

    /** @test */
    function bill_payment_request_is_saved_to_db()
    {
        $this->mock->append(
            new Response(200, ['Content_type' => 'application/json'],
                json_encode($this->mockResponses['request_success'])));

        $res = (new Utility($this->_client))->billPayment(765432100, 10, Providers::DSTV, 765432100);

        $this->assertIsArray($res);
    }

    /** @test */
    function bill_payment_fails_on_invalid_provider()
    {
        $this->expectException(KyandaException::class);

        (new Utility($this->_client))->billPayment(765432100, 10, Providers::SAFARICOM, 765432100);
    }
}