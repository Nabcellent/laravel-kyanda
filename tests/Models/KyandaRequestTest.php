<?php

namespace Nabcellent\Kyanda\Tests\Models;

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Nabcellent\Kyanda\Models\KyandaRequest;
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
}
