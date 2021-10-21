<?php

namespace Nabcellent\Kyanda\Tests\Library;

use Illuminate\Support\Facades\Config;
use Nabcellent\Kyanda\Facades\Account;
use Nabcellent\Kyanda\Tests\TestCase;

//    TODO: Should we mock the api for these type of tests? CAN we mock?
class AccountTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // additional setup
        Config::set('kyanda.api_key', 'somethinggoeshere');
        Config::set('kyanda.merchant_id', 'somethinggoeshere');
    }

    /** @test */
    function balance()
    {
        $bal = Account::balance();

        $this->assertIsArray($bal);
    }

    /** @test */
    function trasnsaction_status()
    {
        $status = Account::transactionStatus("KYAAPI___");

        $this->assertIsArray($status);
    }
}