<?php

namespace Nabcellent\Kyanda\Library;

use GuzzleHttp\Exception\GuzzleException;
use Nabcellent\Kyanda\Exceptions\KyandaException;

/**
 * Class Account
 * @package Nabcellent\Kyanda\Library
 */
class Account extends Core
{
    //  Check account balance
    /**
     * @return array
     * @throws KyandaException
     * @throws GuzzleException
     */
    public function balance(): array
    {
        return $this->request('account_balance', []);
    }



    //  Check transaction status
    /**
     * @param string $reference
     * @return array
     * @throws KyandaException
     * @throws GuzzleException
     */
    public function transactionStatus(string $reference): array
    {
        $this->attachMerchantStart = true;

        $body = [
            "transactionRef" => $reference,
        ];

        return $this->request('transaction_status', $body);
    }
}
