<?php

namespace AkramGhaleb\LaravelGrandstream\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \AkramGhaleb\LaravelGrandstream\LaravelGrandstream
 */
class Grandstream extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \AkramGhaleb\LaravelGrandstream\LaravelGrandstream::class;
    }
}
