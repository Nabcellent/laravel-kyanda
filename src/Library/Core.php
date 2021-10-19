<?php

namespace Nabcellent\Kyanda\Library;

use GuzzleHttp\ClientInterface;

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
}
