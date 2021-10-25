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
//    TODO: account balance
//    Add Account balance function/process here
    /**
     * @return array
     * @throws KyandaException
     * @throws GuzzleException
     */
    public function balance(): array
    {
        return (array) $this->request('account_balance', []);
    }


//    TODO: transaction status
//    Add transaction check function/process here
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

        return (array) $this->request('transaction_status', $body);
    }
}
