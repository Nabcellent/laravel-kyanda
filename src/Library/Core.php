<?php

namespace Nabcellent\Kyanda\Library;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Support\Str;
use Nabcellent\Kyanda\Exceptions\KyandaException;
use Psr\Http\Message\ResponseInterface;

use function config;
use function json_decode;
use function strlen;

/**
 * Class Core
 *
 * @package Nabcellent\Kyanda\Library
 */
class Core
{
    private BaseClient $baseClient;

    protected bool $attachMerchantStart;

    /**
     *
     * @param BaseClient $baseClient
     * @param bool $attachMerchantStart
     */
    public function __construct(BaseClient $baseClient, bool $attachMerchantStart = false)
    {
        $this->baseClient = $baseClient;
        $this->attachMerchantStart = $attachMerchantStart;
    }

//    TODO: This should be private but figure out testing

    /**
     * @throws KyandaException|GuzzleException
     */
    public function sendRequest(string $endpoint, array $body): ResponseInterface
    {
        $apiKey = config('kyanda.api_key', false);
        if (!$apiKey) {
            throw new KyandaException("No API key specified.");
        }
        $merchantId = config('kyanda.merchant_id', false);
        if (!$merchantId) {
            throw new KyandaException("No Merchant ID specified.");
        }

//        Added these to reduce redundancy in child classes
        $body = $this->attachMerchantStart
            ? ['MerchantID' => $merchantId] + $body
            : $body + ['MerchantID' => $merchantId];
        $body += ['signature' => $this->buildSignature($body)];

        return $this->baseClient->clientInterface->request('POST', $endpoint, [
            'headers' => [
                'apiKey' => $apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => $body,
        ]);
    }

    /**
     * @throws KyandaException|GuzzleException
     */
    public function request(string $endpoint, array $body): array
    {
        $endpoint = Endpoints::build($endpoint);

        try {
            $response = $this->sendRequest($endpoint, $body);
            $_body = json_decode($response->getBody());

            return (array)$_body;
        } catch (ClientException | ServerException $exception) {
            throw new KyandaException($exception->getResponse()->getBody());
        }
    }

    private function buildSignature(array $items): string
    {
//        Check for notification body
        if (isset($items['callbackURL'])) {
            unset($items['callbackURL']);
        }

        $signatureString = implode($items);

        $secretKey = config('kyanda.api_key');

        return hash_hmac('sha256', $signatureString, $secretKey);
    }

    //    TODO: This should be protected at least but figure out testing

    /**
     * @throws KyandaException
     */
    public function getTelcoFromPhone(int $phone): string
    {
        $safReg = '/^(?:254|\+254|0)?((?:7(?:[0129][0-9]|4[0123568]|5[789]|6[89])|(1([1][0-5])))[0-9]{6})$/';
        $airReg = '/^(?:254|\+254|0)?((?:(7(?:(3[0-9])|(5[0-6])|(6[27])|(8[0-9])))|(1([0][0-6])))[0-9]{6})$/';
        $telReg = '/^(?:254|\+254|0)?(7(7[0-9])[0-9]{6})$/';
        $equReg = '/^(?:254|\+254|0)?(7(6[3-6])[0-9]{6})$/';
        $faibaReg = '/^(?:254|\+254|0)?(747[0-9]{6})$/';

        switch (1) {
            case preg_match($safReg, $phone):
                $result = Providers::SAFARICOM;
                break;
            case preg_match($airReg, $phone):
                $result = Providers::AIRTEL;
                break;
            case preg_match($telReg, $phone):
                $result = Providers::TELKOM;
                break;
            case preg_match($equReg, $phone):
                $result = Providers::EQUITEL;
                break;
            case preg_match($faibaReg, $phone):
                $result = Providers::FAIBA;
                break;
            default:
                $result = null;
                break;
        }

        if (!$result) {
            throw new KyandaException("Phone does not seem to be valid or supported");
        }

        return $result;
    }

    /**
     * @throws KyandaException
     */
    public function formatPhoneNumber(string $number, bool $strip_plus = true): string
    {
        $number = preg_replace('/\s+/', '', $number);

        $possibleStartingChars = ['+254', '0', '254', '7', '1'];

        if (!Str::startsWith($number, $possibleStartingChars)) {
            //            Number doesn't have valid starting digits e.g. -0254110000000
            throw new KyandaException("Number does not seem to be a valid phone");
        }

        $replace = static function ($needle, $replacement) use (&$number) {
            if (Str::startsWith($number, $needle)) {
                $pos = strpos($number, $needle);
                $length = strlen($needle);
                $number = substr_replace($number, $replacement, $pos, $length);
            }
        };

        $replace('2547', '07');
        $replace('7', '07');
        $replace('2541', '01');
        $replace('1', '01');

        if ($strip_plus) {
            $replace('+254', '0');
        }

        if (!Str::startsWith($number, "0")) {
            //  Means the number started with correct digits but after replacing,
            //  found invalid digit e.g. 254256000000
            //  2547 isn't found and so 0 does not replace it, which means false number
            throw new KyandaException("Number does not seem to be a valid phone");
        }

        return $number;
    }
}
