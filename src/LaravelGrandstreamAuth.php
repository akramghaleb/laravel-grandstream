<?php

namespace AkramGhaleb\LaravelGrandstream;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class LaravelGrandstreamAuth
{
    private static function base(): string
    {
        return rtrim(config('grandstream.base'), '/');
    }

    private static function user(): string
    {
        return config('grandstream.user');
    }

    private static function pass(): string
    {
        return config('grandstream.pass');
    }

    private static function ver(): string
    {
        return config('grandstream.version');
    }

    private static function ttl(): int
    {
        return (int) config('grandstream.cookie_ttl');
    }

    private static function key(int|string $uid): string
    {
        return "ucm_cookie:{$uid}";
    }

    public static function loginFor(int|string $uid): string
    {
        $base = self::base();
        $user = self::user();
        $pass = self::pass();
        $ver  = self::ver();
        $ttl  = self::ttl();

        // challenge
        $challengeRes = Http::withoutVerifying()
            ->withHeaders(['Content-Type'=>'application/json;charset=UTF-8'])
            ->post("{$base}/api", [
                'request'=>['action'=>'challenge','user'=>$user,'version'=>$ver]
            ])->json();

        $challenge = data_get($challengeRes, 'response.challenge');
        if (! $challenge) {
            throw new \RuntimeException('Challenge failed');
        }

        // token
        $token = md5($challenge.$pass);

        // login
        $loginRes = Http::withoutVerifying()
            ->withHeaders(['Content-Type'=>'application/json;charset=UTF-8'])
            ->post("{$base}/api", [
                'request'=>['action'=>'login','token'=>$token,'user'=>$user,'url'=>url('/noop')]
            ])->json();

        $cookie = data_get($loginRes, 'response.cookie');
        if (! $cookie) {
            throw new \RuntimeException('Login failed');
        }

        Cache::put(self::key($uid), $cookie, now()->addMinutes($ttl));
        return $cookie;
    }

    private static function cookieFor(int|string $uid): ?string
    {
        return Cache::get(self::key($uid));
    }

    public static function api(string $action, array $payload = [], bool $retry = true): array
    {
        $uid = auth()->id();
        $cookie = self::cookieFor($uid) ?? self::loginFor($uid);

        $body = ['request' => array_merge($payload, ['action'=>$action, 'cookie'=>$cookie])];

        $res = Http::withoutVerifying()
            ->withHeaders(['Content-Type'=>'application/json;charset=UTF-8'])
            ->post(self::base()."/api", $body)
            ->json();

        $status = data_get($res, 'status');
        if (($status === -8 || $status === -6 || $status === -37) && $retry) {
            $cookie = self::loginFor($uid);
            $body['request']['cookie'] = $cookie;

            $res = Http::withoutVerifying()
                ->withHeaders(['Content-Type'=>'application/json;charset=UTF-8'])
                ->post(self::base()."/api", $body)
                ->json();
        }

        return $res ?? ['status'=>-500,'response'=>['error'=>'No response']];
    }
}
