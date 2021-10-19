<?php

namespace Nabcellent\Kyanda\Library;

use Nabcellent\Kyanda\Exceptions\KyandaException;
use Nabcellent\Kyanda\Models\KyandaRequest;

/**
 * Class Utility
 * @package Nabcellent\Kyanda\Library
 */
class Utility extends Core
{
//    TODO: airtime purchase
//    Add airtime purchase function/process here
    /**
     * @throws KyandaException
     */
    function airtime_purchase(int $phone, int $amount): array
    {
        $telco = $this->getTelcoFromPhone($phone);
        $phone = $this->formatPhoneNumber($phone);

//        TODO: Amount Limits? Amount validation?
//        TODO: Should we allow initiator phone as fn parameter?
        $body = [
            'phone' => $phone,
            'amount' => $amount,
            'telco' => $telco,
            'initiatorPhone' => $phone,
        ];

        $response = (array)$this->request('transaction_status', $body);

        return $this->saveRequest($response);
    }

//    TODO: bill payment
//    Add bill payment function/process here



// END Bill payment


    /**
     * @throws KyandaException
     */
    private function saveRequest(array $response)
    {
        if ($response['status_code'] == 0000) {
            $request = KyandaRequest::create([
                'status_code' => $response['status_code'],
                'status' => $response['status'],
                'merchant_reference' => $response['transactionId'],
                'message' => $response['transactiontxt']
            ]);

//            TODO: Should we make multiple event types? i.e. KyandaAirtimeRequestEvent, KyandaBillRequestEvent ...
//            event(new KyandaRequestEvent($request));
            return $request;
        }

        throw new KyandaException($response['transactiontxt']);
    }
}
