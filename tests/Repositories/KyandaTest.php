<?php

namespace Nabcellent\Kyanda\Tests\Repositories;

use GuzzleHttp\Psr7\Response;
use Nabcellent\Kyanda\Models\KyandaRequest;
use Nabcellent\Kyanda\Models\KyandaTransaction;
use Nabcellent\Kyanda\Repositories\Kyanda;
use Nabcellent\Kyanda\Tests\MockServerTestCase;

class KyandaTest extends MockServerTestCase
{

    private $repository;

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        $this->repository = new Kyanda($this->_client);

        KyandaRequest::create([
            'status_code' => '0000',
            'status' => 'Success',
            'merchant_reference' => 'KYAAPI677833',
            'message' => 'Your request has been posted successfully!'
        ]);

        KyandaRequest::create([
            'status_code' => '0000',
            'status' => 'Success',
            'merchant_reference' => 'KYAAPI677834',
            'message' => 'Your request has been posted successfully!'
        ]);
    }

    /** @test */
    function query_transaction_statuses()
    {
        $this->mock->append(
            new Response(200, ['Content_type' => 'application/json'],
                json_encode($this->mockResponses['query_transaction_status'])));
        $this->mock->append(
            new Response(200, ['Content_type' => 'application/json'],
                json_encode($this->mockResponses['transaction_status_failed'])));

        $res = $this->repository->queryTransactionStatus();

        $transaction = KyandaTransaction::whereTransactionReference("KYAAPI677833")->first();

        $this->assertEquals($res['successful']['KYAAPI677833'], $transaction->status);
    }


    /** @test */
    function query_transaction_statuses_server_error()
    {
        $this->mock->append(
            new Response(404, ['Content_type' => 'application/json'],
                json_encode($this->mockResponses['transaction_status'])));

        $res = $this->repository->queryTransactionStatus();

        $this->assertIsString($res['errors']['KYAAPI677833']);
    }
}