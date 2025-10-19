<?php

namespace AkramGhaleb\LaravelGrandstream\Traits;

trait SystemConfiguration
{
    /*
     * The “getSystemStatus” action will return the system information.
     */
    public static function getSystemStatus()
    {
        return self::getData('getSystemStatus');
    }

    /*
     * The “getSystemGeneralStatus” action will return the version information
     */
    public static function getSystemGeneralStatus():array
    {
        return self::getData('getSystemGeneralStatus');
    }
}
