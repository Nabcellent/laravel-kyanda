<?php

namespace Nabcellent\Kyanda\Library;

use GuzzleHttp\Exception\GuzzleException;
use Nabcellent\Kyanda\Events\KyandaRequestEvent;
use Nabcellent\Kyanda\Exceptions\KyandaException;
use Nabcellent\Kyanda\Models\KyandaRequest;

/**
 * Class Utility
 *
 * @package Nabcellent\Kyanda\Library
 */
class Utility extends Core
{
//    TODO: airtime purchase
//    Add airtime purchase function/process here
    /**
     * @param int $phone
     * @param int $amount
     * @param int|null $relationId
     * @param bool $save
     * @return array|KyandaRequest
     * @throws GuzzleException
     * @throws KyandaException
     */
    public function airtimePurchase(int $phone, int $amount, int $relationId = null, bool $save = true): array
    {
        $telco = $this->getTelcoFromPhone($phone);
        $phone = $this->formatPhoneNumber($phone);

//        TODO: Amount Limits? Amount validation?
//        TODO: Should we allow initiator phone as fn parameter?
        $body = [
            'amount'         => $amount,
            'phone'          => $phone,
            'telco'          => $telco,
            'initiatorPhone' => $phone,
        ];

        $response = $this->request('airtime', $body);

        if ($save) {
            return (array) $this->saveRequest($response, $relationId);
        }

        return $response;
    }

//    TODO: bill payment
//    Add bill payment function/process here
    /**
     * @param int $accountNo
     * @param int $amount
     * @param string $provider
     * @param int $phone
     * @param int|null $relationId
     * @param bool $save
     * @return array|KyandaRequest
     * @throws GuzzleException
     * @throws KyandaException
     */
    public function billPayment(
        int $accountNo,
        int $amount,
        string $provider,
        int $phone,
        int $relationId = null,
        bool $save = true
    ): array {
//        TODO: Should we allow initiator phone as fn parameter?

//        TODO: Refactor this to testable function...seems ok
        $allowedProviders = [
            Providers::KPLC_PREPAID,
            Providers::KPLC_POSTPAID,
            Providers::GOTV,
            Providers::DSTV,
            Providers::ZUKU,
            Providers::STARTIMES,
            Providers::NAIROBI_WTR
        ];

        if (!in_array(strtoupper($provider), $allowedProviders)) {
            throw new KyandaException("Provider does not seem to be valid or supported");
        }

        $phone = $this->formatPhoneNumber($phone);

//        TODO: Confirm whether initiator phone is necessary
        $body = [
            'amount'         => $amount,
            'account'        => $accountNo,
            'telco'          => $provider,
            'initiatorPhone' => $phone,
        ];

        $response = $this->request('bill', $body);

        if ($save) {
            return (array) $this->saveRequest($response, $relationId);
        }

        return $response;
    }


    /**
     * @throws KyandaException
     */
    private function saveRequest(array $response, int $relationId = null): KyandaRequest
    {
        if ($response['status_code'] == 0000) {
            $request = KyandaRequest::create([
                'status_code'        => $response['status_code'],
                'status'             => $response['status'],
                'merchant_reference' => $response['transactionId'],
                'message'            => $response['transactiontxt'],
                'relation_id'        => $relationId
            ]);

//            TODO: Should we make multiple event types? i.e. KyandaAirtimeRequestEvent, KyandaBillRequestEvent ...
            /** @var KyandaRequest $request */
            event(new KyandaRequestEvent($request));
            return $request;
        }

//        TODO: We should throw relevant exceptions based on api response
        throw new KyandaException($response['transactiontxt']);
    }
}
