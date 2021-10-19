<?php

namespace Nabcellent\Kyanda\Library;

use Nabcellent\Kyanda\Exceptions\KyandaException;

class Endpoints
{
    /**
     * @throws KyandaException
     */
    private static function getEndpoint($section): string
    {
        $list = [
            'account_balance' => '/billing/v1/account-balance',
            'transaction_status' => '/billing/v1/transaction-check',
            'send_mobile' => '/billing/v1/mobile-payout/create',
            'send_bank' => '/billing/v1/bank-payout/create',
            'stk_push' => '/billing/v1/checkout/create',
            'airtime' => '/billing/v1/airtime/create',
            'bill' => '/billing/v1/bill/create',
            'callback_register' => '/billing/v1/callback-url/create',
        ];
        if ($item = $list[$section]) {
            return self::getUrl($item);
        }
        throw new KyandaException('Unknown endpoint');
    }

    /**
     * @param string $suffix
     * @return string
     */
    private static function getUrl($suffix): string
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
    public static function build($endpoint)
    {
        return self::getEndpoint($endpoint);
    }
}
