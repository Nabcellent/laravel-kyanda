<?php

namespace Nabcellent\Kyanda\Library;

use Nabcellent\Kyanda\Exceptions\KyandaException;

/**
 * Class Account
 * @package Nabcellent\Kyanda\Library
 */
class Account extends Core
{
    protected bool $attachMerchantStart = true;

//    TODO: account balance
//    Add Account balance function/process here
    /**
     * @throws KyandaException
     */
    function balance(): array
    {
        return (array) $this->request('account_balance', []);
    }


//    TODO: transaction status
//    Add transaction check function/process here
    /**
     * @throws KyandaException
     */
    function transaction_status(string $reference): array
    {
        $body = [
            "transactionRef" => $reference,
        ];

        return (array) $this->request('transaction_status', $body);
    }
}
