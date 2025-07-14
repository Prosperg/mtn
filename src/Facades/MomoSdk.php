<?php

namespace Pkl\Mtn\MomoSdk\Facades;

use Illuminate\Support\Facades\Facade;

class MomoSdk extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'momosdk';
    }
}
