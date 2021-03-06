<?php

namespace Nabcellent\Kyanda\Library;

use Nabcellent\Kyanda\Exceptions\KyandaException;

class Endpoints
{
//    TODO: Can we use constants for better value checks? Endpoints::AccountBalance?

    private const URL_LIST = [
        'account_balance' => '/billing/v1/account-balance',
        'transaction_status' => '/billing/v1/transaction-check',
        'send_mobile' => '/billing/v1/mobile-payout/create',
        'send_bank' => '/billing/v1/bank-payout/create',
        'stk_push' => '/billing/v1/checkout/create',
        'airtime' => '/billing/v1/airtime/create',
        'bill' => '/billing/v1/bill/create',
        'callback_register' => '/billing/v1/callback-url/create',

        'test' => '/',
    ];

//    TODO: Refactor to use match if possible
    /**
     * @throws KyandaException
     */
    private static function getEndpoint(string $section): string
    {
        if (isset(self::URL_LIST[$section])) {
            return self::getUrl(self::URL_LIST[$section]);
        }

        throw new KyandaException('Unknown endpoint');
    }

    /**
     * @param string $suffix
     * @return string
     * @throws KyandaException
     */
    private static function getUrl(string $suffix): string
    {
        $baseEndpoint = config('kyanda.urls.base', false);

        if (!$baseEndpoint) {
            throw new KyandaException("No base url specified.");
        }

        return $baseEndpoint . $suffix;
    }

    /**
     * @throws KyandaException
     */
    public static function build(string $endpoint): string
    {
        return self::getEndpoint($endpoint);
    }
}
