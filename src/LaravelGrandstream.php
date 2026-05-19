<?php

namespace AkramGhaleb\LaravelGrandstream;

use AkramGhaleb\LaravelGrandstream\Traits\Extension;
use AkramGhaleb\LaravelGrandstream\Traits\IVR;
use AkramGhaleb\LaravelGrandstream\Traits\PagingIntercom;
use AkramGhaleb\LaravelGrandstream\Traits\SystemConfiguration;
use AkramGhaleb\LaravelGrandstream\Traits\VoiceCall;

class LaravelGrandstream
{
    use Extension;
    use IVR;
    use PagingIntercom;
    use SystemConfiguration;
    use VoiceCall;

    public static function getData(string $action, array $payload = []): array
    {
        $res = LaravelGrandstreamAuth::api($action, $payload);

        return response()->json($res)->original ?? [];
    }
}
