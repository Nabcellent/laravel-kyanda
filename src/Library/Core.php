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
    function sendRequest($endpoint, $body): ResponseInterface
    {
        $apiKey = \config('kyanda.api_key', false);
        if (!$apiKey) {
            throw new KyandaException("No API key specified.");
        }

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
    public function makeRequest($body, $endpoint)
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

    /**
     * @param ClientException $exception
     * @return KyandaException
     */
    private function generateException(ClientException $exception): KyandaException
    {
        return new KyandaException($exception->getResponse()->getBody());
    }

}
