<?php

namespace Nabcellent\Kyanda\Tests\Library;

use GuzzleHttp\Psr7\Response;
use Nabcellent\Kyanda\Tests\MockServerTestCase;

//    TODO: Should we mock the api for these type of tests? CAN we mock?
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
}