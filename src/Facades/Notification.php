<?php

namespace Nabcellent\Kyanda\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array registerCallbackURL(string $url = null)
 *
 * @see \Nabcellent\Kyanda\Library\Notification
 */
class Notification extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Nabcellent\Kyanda\Library\Notification::class;
    }
}
