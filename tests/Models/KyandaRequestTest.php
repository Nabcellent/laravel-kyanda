<?php

namespace Nabcellent\Kyanda\Tests\Models;

use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Nabcellent\Kyanda\Models\KyandaRequest;
use Nabcellent\Kyanda\Models\KyandaTransaction;
use Nabcellent\Kyanda\Tests\TestCase;

class KyandaRequestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function a_request_has_correct_attributes()
    {
        $request = KyandaRequest::create([
            'status_code' => '0000',
            'status' => 'Success',
            'merchant_reference' => 'KYAAPI677833',
            'message' => 'Your request has been posted successfully!'
        ]);

        $this->assertEquals(0000, $request->status_code);
        $this->assertEquals('Success', $request->status);
        $this->assertEquals('KYAAPI677833', $request->merchant_reference);
        $this->assertEquals('Your request has been posted successfully!', $request->message);
    }

    /** @test */
    function a_request_has_unique_merchant_reference()
    {
        KyandaRequest::create([
            'status_code' => '0000',
            'status' => 'Success',
            'merchant_reference' => 'KYAAPI677833',
            'message' => 'Your request has been posted successfully!'
        ]);

        try {
            KyandaRequest::create([
                'status_code' => '0000',
                'status' => 'Success',
                'merchant_reference' => 'KYAAPI677833',
                'message' => 'Your request has been posted successfully!'
            ]);
        } catch (QueryException $e) {
            $this->assertStringContainsString("UNIQUE constraint failed", $e->getMessage());
        }
    }

    /** @test */
    function a_request_has_one_transaction()
    {
        $request = KyandaRequest::create([
            'status_code' => '0000',
            'status' => 'Success',
            'merchant_reference' => 'KYAAPI677833',
            'message' => 'Your request has been posted successfully!'
        ]);

        $transaction = KyandaTransaction::create([
            'transaction_reference' => 'KYAAPI677833',
            'category' => 'UtilityPayment',
            'source' => 'PaymentWallet',
            'destination' => '0715330000',
            'merchant_id' => 'kyanda',
            'details' => ['biller_receiptNo' => '0105781244210'],
            'status' => 'Success',
            'status_code' => '0000',
            'message' => 'Your request has been processed successfully.',
            'amount' => '1500',
            'transaction_date' => Carbon::createFromFormat('Ymdhis', '20210401091002'),
        ]);

        $requestTransaction = $request->transaction;

        $this->assertEquals($requestTransaction->status_code, (int)$transaction->status_code);
        $this->assertEquals($requestTransaction->status, $transaction->status);
        $this->assertEquals($requestTransaction->transaction_reference, $request->merchant_reference);
        $this->assertEquals($requestTransaction->amount, $transaction->amount);
    }
}
