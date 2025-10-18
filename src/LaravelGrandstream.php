<?php

namespace AkramGhaleb\LaravelGrandstream;

use AkramGhaleb\LaravelGrandstream\Traits\Extension;
use AkramGhaleb\LaravelGrandstream\Traits\SystemConfiguration;
use AkramGhaleb\LaravelGrandstream\Traits\VoiceCall;

class LaravelGrandstream
{
    use SystemConfiguration;
    use Extension;
    use VoiceCall;

    public static function getData(string $action, array $payload = []): array
    {
        $res = LaravelGrandstreamAuth::api($action, $payload);

        return response()->json($res)->original ?? [];
    }
}
