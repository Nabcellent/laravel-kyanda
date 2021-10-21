<?php

namespace Nabcellent\Kyanda\Library;

use GuzzleHttp\ClientInterface;

/**
 * Class BaseClient
 *
 * @package Nabcellent\Kyanda\Library
 */
class BaseClient
{
    public function __construct(
        /**
         * @var ClientInterface
         */
        public ClientInterface $clientInterface,

        /**
         * @var bool
         * Determine whether merchant id will be attached at the start or end
         */
        protected bool            $attachMerchantStart = false
    )
    {
    }
}