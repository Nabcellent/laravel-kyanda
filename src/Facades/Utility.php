<?php

namespace Nabcellent\Kyanda\Facades;

use Illuminate\Support\Facades\Facade;
use Nabcellent\Kyanda\Models\KyandaRequest;

/**
 * @method static array|KyandaRequest airtimePurchase(int $phone, int $amount)
 * @method static array|KyandaRequest billPayment(int $accountNumber, int $amount, string $provider, int $phone)
 *
 * @see \Nabcellent\Kyanda\Library\Utility
 */
class Utility extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Nabcellent\Kyanda\Library\Utility::class;
    }
}
