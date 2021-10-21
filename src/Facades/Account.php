<?php

namespace Nabcellent\Kyanda\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array balance()
 * @method static array transactionStatus(string $reference)
 *
 * @see Nabcellent\Kyanda\Library\Account
 */
class Account extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Nabcellent\Kyanda\Library\Account::class;
    }
}
