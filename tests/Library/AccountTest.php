<?php

namespace Nabcellent\Kyanda\Tests\Library;

use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Config;
use Nabcellent\Kyanda\Exceptions\KyandaException;
use Nabcellent\Kyanda\Facades\Account;
use Nabcellent\Kyanda\Tests\MockServerTestCase;


class AccountTest extends MockServerTestCase
{

    /** @test */
    function balance()
    {
        $this->mock->append(
            new Response(200, ['Content_type' => 'application/json'],
                json_encode($this->mockResponses['balance'])));

        $bal = (new \Nabcellent\Kyanda\Library\Account($this->_client))->balance();

        $this->assertIsArray($bal);
        $this->assertEquals(399930, $bal['Account_Bal']);
    }

    /** @test */
    function transaction_status()
    {
        $this->mock->append(
            new Response(200, ['Content_type' => 'application/json'],
                json_encode($this->mockResponses['transaction_status'])));

        $status = (new \Nabcellent\Kyanda\Library\Account($this->_client))->transactionStatus("KYAAPI___");

        $this->assertIsArray($status);
        $this->assertEquals(200, $status['status']);
    }

    /** @test */
    function test_account_facade()
    {
//        This is to enable code coverage for Account facade
        $this->expectException(KyandaException::class);

        Config::set('kyanda.api_key');

        Account::transactionStatus("KYAAPI___");
    }
}