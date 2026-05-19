<?php

use AkramGhaleb\LaravelGrandstream\Facades\Grandstream;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    config([
        'grandstream.base' => 'https://ucm.example.com',
    ]);

    Cache::put('ucm_cookie:123', 'mocked-cookie', now()->addMinutes(10));
    $user = Mockery::mock('Illuminate\Contracts\Auth\Authenticatable');
    $user->shouldReceive('getAuthIdentifier')->andReturn(123);
    auth()->login($user);
});

it('can list accounts with default and custom options', function () {
    Http::fake([
        'https://ucm.example.com/api' => Http::response([
            'status' => 0,
            'response' => ['accounts' => [['extension' => '1000', 'fullname' => 'Admin']]],
        ]),
    ]);

    // Test with default arguments
    $resDefault = Grandstream::listAccount();

    expect($resDefault)->toBe([
        'status' => 0,
        'response' => ['accounts' => [['extension' => '1000', 'fullname' => 'Admin']]],
    ]);

    Http::assertSent(function ($request) {
        return $request['request']['action'] === 'listAccount' &&
            $request['request']['options'] === 'extension,account_type,fullname,status,addr' &&
            $request['request']['sidx'] === 'extension' &&
            $request['request']['sord'] === 'asc' &&
            $request['request']['page'] === 1;
    });

    // Test with custom arguments
    Grandstream::listAccount('extension,status', 'status', 'desc', 2);

    Http::assertSent(function ($request) {
        return $request['request']['action'] === 'listAccount' &&
            $request['request']['options'] === 'extension,status' &&
            $request['request']['sidx'] === 'status' &&
            $request['request']['sord'] === 'desc' &&
            $request['request']['page'] === 2;
    });
});

it('can get a SIP account details', function () {
    Http::fake([
        'https://ucm.example.com/api' => Http::response([
            'status' => 0,
            'response' => ['extension' => '1001', 'fullname' => 'Agent 1'],
        ]),
    ]);

    $res = Grandstream::getSIPAccount('1001');

    expect($res)->toBe([
        'status' => 0,
        'response' => ['extension' => '1001', 'fullname' => 'Agent 1'],
    ]);

    Http::assertSent(function ($request) {
        return $request['request']['action'] === 'getSIPAccount' &&
            $request['request']['extension'] === '1001';
    });
});

it('can update a SIP account', function () {
    Http::fake([
        'https://ucm.example.com/api' => Http::response([
            'status' => 0,
            'response' => ['status' => 'updated'],
        ]),
    ]);

    // Test with default permission
    $resDefault = Grandstream::updateSIPAccount('1002');
    expect($resDefault)->toBe([
        'status' => 0,
        'response' => ['status' => 'updated'],
    ]);

    Http::assertSent(function ($request) {
        return $request['request']['action'] === 'updateSIPAccount' &&
            $request['request']['extension'] === '1002' &&
            $request['request']['permission'] === 'internal';
    });

    // Test with custom permission
    Grandstream::updateSIPAccount('1002', 'national');

    Http::assertSent(function ($request) {
        return $request['request']['action'] === 'updateSIPAccount' &&
            $request['request']['extension'] === '1002' &&
            $request['request']['permission'] === 'national';
    });
});
