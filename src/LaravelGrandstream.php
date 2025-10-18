<?php

namespace AkramGhaleb\LaravelGrandstream;

class LaravelGrandstream
{
    public static function getData(string $action, array $payload = []): array
    {
        $res = LaravelGrandstreamAuth::api($action, $payload);

        return response()->json($res)->original ?? [];
    }
}
