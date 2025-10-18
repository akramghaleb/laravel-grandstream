<?php

namespace AkramGhaleb\LaravelGrandstream\Traits;

trait VoiceCall
{
    public static function listBridgedChannels():array
    {
        return self::getData('listBridgedChannels');
    }

    public static function listUnBridgedChannels():array
    {
        return self::getData('listUnBridgedChannels');
    }
}
