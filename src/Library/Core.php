<?php

namespace Nabcellent\Kyanda\Library;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use Nabcellent\Kyanda\Exceptions\KyandaException;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Core
 *
 * @package Nabcellent\Kyanda\Library
 */
class Core
{
    /**
     * @var ClientInterface
     */
    public ClientInterface $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }


    /**
     * @throws KyandaException
     */
    function sendRequest(string $endpoint, array $body): ResponseInterface
    {
        $apiKey = \config('kyanda.api_key', false);
        if (!$apiKey) {
            throw new KyandaException("No API key specified.");
        }
        $merchantId = \config('kyanda.merchant_id', false);
        if (!$merchantId) {
            throw new KyandaException("No Merchant ID specified.");
        }

//        Added these to reduce redundancy in child classes
        $body = ['merchantID' => $merchantId] + $body;
        $body += ['signature' => $this->buildSignature($body)];

        return $this->client->request(
            'POST',
            $endpoint,
            [
                'headers' => [
                    'apiKey' => $apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => $body,
            ]
        );
    }

    /**
     * @throws KyandaException
     */
    public function request(string $endpoint, array $body)
    {
        $endpoint = Endpoints::build($endpoint);
        try {
            $response = $this->sendRequest($endpoint, $body);
            $_body = \json_decode($response->getBody());
            if ($response->getStatusCode() !== 200) {
                throw new KyandaException($_body->errorMessage ? $_body->errorCode . ' - ' . $_body->errorMessage : $response->getBody());
            }
            return $_body;
        } catch (ClientException $exception) {
            throw $this->generateException($exception);
        }
    }

    function buildSignature(array $items): bool|string
    {
        $signatureString = implode($items);

        $secretKey = config('kyanda.api_key');

        return hash_hmac('sha256', $signatureString, $secretKey);
    }

    /**
     * @param ClientException $exception
     * @return KyandaException
     */
    private function generateException(ClientException $exception): KyandaException
    {
        return new KyandaException($exception->getResponse()->getBody());
    }
}
