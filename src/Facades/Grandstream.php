<?php

namespace AkramGhaleb\LaravelGrandstream\Facades;

use AkramGhaleb\LaravelGrandstream\LaravelGrandstream;
use Illuminate\Support\Facades\Facade;

/**
 * @see LaravelGrandstream
 */
class Grandstream extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return LaravelGrandstream::class;
    }
}
