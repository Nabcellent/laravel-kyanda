<?php

namespace Nabcellent\Kyanda\Library;

use GuzzleHttp\ClientInterface;

/**
 * Class BaseClient
 *
 * @package Nabcellent\Kyanda\Library\
 */
class BaseClient
{
    public ClientInterface $clientInterface;

    protected bool $attachMerchantStart;

    /**
     *
     * @param ClientInterface $clientInterface
     * @param bool            $attachMerchantStart
     * @var bool
     * Determine whether merchant id will be attached at the start or end
     */
    public function __construct(ClientInterface $clientInterface, bool $attachMerchantStart = false)
    {
        $this->clientInterface = $clientInterface;
        $this->attachMerchantStart = $attachMerchantStart;
    }
}
