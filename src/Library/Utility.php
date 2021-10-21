<?php

namespace Nabcellent\Kyanda\Library;

use GuzzleHttp\Exception\GuzzleException;
use Nabcellent\Kyanda\Events\KyandaRequestEvent;
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
     * @param int $phone
     * @param int $amount
     * @return KyandaRequest
     * @throws GuzzleException
     * @throws KyandaException
     */
    public function airtimePurchase(int $phone, int $amount): KyandaRequest
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

        $response = (array)$this->request('airtime', $body);

        return $this->saveRequest($response);
    }

//    TODO: bill payment
//    Add bill payment function/process here
    /**
     * @param int $accountNumber
     * @param int $amount
     * @param string $provider
     * @return KyandaRequest
     * @throws KyandaException
     * @throws GuzzleException
     */

    public function billPayment(int $accountNumber, int $amount, string $provider): KyandaRequest
    {
//        TODO: Should we allow initiator phone as fn parameter?

//        TODO: Refactor this to testable function
        $allowedProviders = [
            Providers::KPLC_PREPAID, Providers::KPLC_POSTPAID,
            Providers::GOTV, Providers::DSTV, Providers::ZUKU, Providers::STARTIMES,
            Providers::NAIROBI_WTR
        ];

        if (!in_array(strtoupper($provider), $allowedProviders)) {
            throw new KyandaException("Provider does not seem to be valid or supported");
        }

//        TODO: Confirm whether initiator phone is necessary
        $body = [
            'account' => $accountNumber,
            'amount' => $amount,
            'telco' => $provider,
//            'initiatorPhone' => $phone,
        ];

            $response = (array)$this->request('bill', $body);

            return $this->saveRequest($response);
    }


    /**
     * @throws KyandaException
     * @noinspection PhpUndefinedMethodInspection
     */
    private function saveRequest(array $response): KyandaRequest
    {
        if ($response['status_code'] == 0000) {
            $request = KyandaRequest::create([
                'status_code' => $response['status_code'],
                'status' => $response['status'],
                'merchant_reference' => $response['transactionId'],
                'message' => $response['transactiontxt']
            ]);

//            TODO: Should we make multiple event types? i.e. KyandaAirtimeRequestEvent, KyandaBillRequestEvent ...
            event(new KyandaRequestEvent($request));
            return $request;
        }

        throw new KyandaException($response['transactiontxt']);
    }
}
