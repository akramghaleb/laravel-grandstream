<?php

use AkramGhaleb\LaravelGrandstream\Facades\Grandstream;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    config([
        'grandstream.base' => 'https://ucm.example.com',
    ]);

    // Pre-populate cache so we bypass the login flow and test the trait methods directly
    Cache::put('ucm_cookie:123', 'mocked-cookie', now()->addMinutes(10));
    $user = Mockery::mock('Illuminate\Contracts\Auth\Authenticatable');
    $user->shouldReceive('getAuthIdentifier')->andReturn(123);
    auth()->login($user);
});

it('can get system status', function () {
    Http::fake([
        'https://ucm.example.com/api' => Http::response([
            'status' => 0,
            'response' => ['cpu_usage' => '12%', 'mem_usage' => '45%'],
        ]),
    ]);

    $res = Grandstream::getSystemStatus();

    expect($res)->toBe([
        'status' => 0,
        'response' => ['cpu_usage' => '12%', 'mem_usage' => '45%'],
    ]);

    Http::assertSent(function ($request) {
        return $request['request']['action'] === 'getSystemStatus' &&
            $request['request']['cookie'] === 'mocked-cookie';
    });
});

it('can get system general status', function () {
    Http::fake([
        'https://ucm.example.com/api' => Http::response([
            'status' => 0,
            'response' => ['version' => '1.0.0.1'],
        ]),
    ]);

    $res = Grandstream::getSystemGeneralStatus();

    expect($res)->toBe([
        'status' => 0,
        'response' => ['version' => '1.0.0.1'],
    ]);

    Http::assertSent(function ($request) {
        return $request['request']['action'] === 'getSystemGeneralStatus' &&
            $request['request']['cookie'] === 'mocked-cookie';
    });
});
