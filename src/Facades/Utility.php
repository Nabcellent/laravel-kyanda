<?php

namespace Nabcellent\Kyanda\Facades;

use Illuminate\Support\Facades\Facade;
use Nabcellent\Kyanda\Models\KyandaRequest as KR;

/**
 * @method static KR airtimePurchase(int $phone, int $amount, int $relationId = null)
 * @method static KR billPayment(int $account, int $amount, string $provider, int $phone, int $relationId = null)
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
