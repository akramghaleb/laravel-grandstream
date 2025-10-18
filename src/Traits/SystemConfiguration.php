<?php

namespace AkramGhaleb\LaravelGrandstream\Traits;

trait SystemConfiguration
{
    public static function getSystemStatus()
    {
        return self::getData('getSystemStatus');
    }

    public static function getSystemGeneralStatus():array
    {
        return self::getData('getSystemGeneralStatus');
    }
}
