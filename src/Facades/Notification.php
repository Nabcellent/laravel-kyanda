<?php

namespace Nabcellent\Kyanda\Facades;

use Illuminate\Support\Facades\Facade;

class Notification extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Nabcellent\Kyanda\Library\Utility::class;
    }
}
