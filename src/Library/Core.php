<?php

namespace Nabcellent\Src\Library;

use GuzzleHttp\ClientInterface;

/**
 * Class Core
 *
 * @package Nabcellent\Src\Library
 */
class Core {
    /**
     * @var ClientInterface
     */
    public ClientInterface $client;

    public function __construct(ClientInterface $client) {
        $this->client = $client;
    }
}
