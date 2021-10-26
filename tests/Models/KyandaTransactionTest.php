<?php

namespace Nabcellent\Kyanda\Tests\Models;

use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Nabcellent\Kyanda\Models\KyandaRequest;
use Nabcellent\Kyanda\Models\KyandaTransaction;
use Nabcellent\Kyanda\Tests\TestCase;

class KyandaTransactionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function a_transaction_has_correct_attributes()
    {
        $request = KyandaTransaction::factory()->create([
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

        $this->assertEquals('KYAAPI677833', $request->transaction_reference);
        $this->assertEquals('UtilityPayment', $request->category);
        $this->assertEquals('PaymentWallet', $request->source);
        $this->assertEquals('0715330000', $request->destination);
        $this->assertEquals('kyanda', $request->merchant_id);
        $this->assertEquals('Success', $request->status);
        $this->assertEquals(0000, $request->status_code);
        $this->assertEquals('Your request has been processed successfully.', $request->message);
        $this->assertEquals(['biller_receiptNo' => '0105781244210'], $request->details);
        $this->assertEquals(1500, $request->amount);
        $this->assertEquals(Carbon::createFromFormat('Ymdhis', '20210401091002'), $request->transaction_date);
    }

    /** @test */
    function a_transaction_has_unique_transaction_reference()
    {
        KyandaTransaction::factory()->create([
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

        try {
            KyandaTransaction::factory()->create([
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
        } catch (QueryException $e) {
            $this->assertStringContainsString("UNIQUE constraint failed", $e->getMessage());
        }
    }

    /** @test */
    function a_request_has_one_transaction()
    {
        $request = KyandaRequest::factory()->create([
            'status_code' => '0000',
            'status' => 'Success',
            'merchant_reference' => 'KYAAPI677833',
            'message' => 'Your request has been posted successfully!'
        ]);

        $transaction = KyandaTransaction::factory()->create([
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

        $transactionRequest = $transaction->request;

        $this->assertEquals($transaction->transaction_reference, $transactionRequest->merchant_reference);
    }
}
