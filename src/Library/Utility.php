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

    private string $provider;

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
        $this->validate("AIRTIME", $amount);

        $this->provider = $this->getTelcoFromPhone($phone);
        $phone = $this->formatPhoneNumber($phone);

//        TODO: Amount Limits? Amount validation?
//        TODO: Should we allow initiator phone as fn parameter?
        $body = [
            'amount' => $amount,
            'phone' => $phone,
            'telco' => $this->provider,
            'initiatorPhone' => $phone,
        ];

        $response = $this->request('airtime', $body);

        if ($save) {
            return (array)$this->saveRequest($response, $relationId);
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
        $allowedProviders = [
            Providers::KPLC_PREPAID,
            Providers::KPLC_POSTPAID,
            Providers::GOTV,
            Providers::DSTV,
            Providers::ZUKU,
            Providers::STARTIMES,
            Providers::NAIROBI_WTR
        ];

        $this->validate($provider, $amount);

        if (!in_array(strtoupper($provider), $allowedProviders)) {
            throw new KyandaException("Provider does not seem to be valid or supported");
        }

        $this->provider = $provider;

        $phone = $this->formatPhoneNumber($phone);

//        TODO: Confirm whether initiator phone is necessary
        $body = [
            'amount' => (string)$amount,
            'account' => (string)$accountNo,
            'telco' => $this->provider,
            'initiatorPhone' => $phone,
        ];

        $response = $this->request('bill', $body);

        if ($save) {
            return (array)$this->saveRequest($response, $relationId);
        }

        return $response;
    }


    /**
     * @throws KyandaException
     */
    private function saveRequest(array $response, int $relationId = null): KyandaRequest
    {
        try {
            $request = KyandaRequest::create([
                'status_code' => $response['status_code'],
                'status' => $response['status'],
                'merchant_reference' => $response['merchant_reference'],
                'message' => $response['transactiontxt'],
                'provider' => $this->provider,
                'relation_id' => $relationId
            ]);

//            /** @var KyandaRequest $request */
            event(new KyandaRequestEvent($request));
            return $request;
        } catch (\Exception $e) {
//        TODO: We should throw relevant exceptions based on api response
            throw new KyandaException($e->getMessage());
        }
    }


    /**
     * @throws KyandaException
     */
    private function validate(string $validationType, int $amount)
    {
        $min = 0;
        $max = 0;

        switch ($validationType) {
            case "AIRTIME":
                $min = config('kyanda.limits.AIRTIME.min', 10);
                $max = config('kyanda.limits.AIRTIME.max', 10000);

                break;

            case Providers::KPLC_POSTPAID:
                $min = config('kyanda.limits.bills.KPLC_POSTPAID.min', 100);
                $max = config('kyanda.limits.bills.KPLC_POSTPAID.max', 35000);

                break;

            default:
                return true;
        }

        if ($amount < $min || $amount > $max) {
            throw new KyandaException("Amount needs to be between $min and $max.");
        }

        return true;
    }
}
