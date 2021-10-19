<?php

namespace Nabcellent\Kyanda\Facades;

use Illuminate\Support\Facades\Facade;

class Core extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Nabcellent\Kyanda\Library\Core::class;
    }
}
