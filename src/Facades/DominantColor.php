<?php

namespace CapsulesCodes\DominantColor\Facades;

use Illuminate\Support\Facades\Facade;

class DominantColor extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'dominant-color';
    }
}
