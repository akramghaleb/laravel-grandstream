<?php

use AkramGhaleb\LaravelGrandstream\LaravelGrandstreamAuth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    config([
        'grandstream.base' => 'https://ucm.example.com',
        'grandstream.user' => 'test-user',
        'grandstream.pass' => 'test-password',
        'grandstream.version' => '1.2',
        'grandstream.cookie_ttl' => 10,
    ]);
});

it('successfully logs in and caches the cookie', function () {
    $uid = 123;
    $challenge = 'mocked-challenge-string';
    $cookie = 'mocked-session-cookie';

    Http::fake([
        'https://ucm.example.com/api' => Http::sequence()
            ->push(['response' => ['challenge' => $challenge]])
            ->push(['response' => ['cookie' => $cookie]]),
    ]);

    $returnedCookie = LaravelGrandstreamAuth::loginFor($uid);

    expect($returnedCookie)->toBe($cookie);
    expect(Cache::get("ucm_cookie:{$uid}"))->toBe($cookie);

    Http::assertSentInOrder([
        function ($request) {
            return $request->url() === 'https://ucm.example.com/api' &&
                $request['request']['action'] === 'challenge' &&
                $request['request']['user'] === 'test-user' &&
                $request['request']['version'] === '1.2';
        },
        function ($request) use ($challenge) {
            $expectedToken = md5($challenge.'test-password');

            return $request->url() === 'https://ucm.example.com/api' &&
                $request['request']['action'] === 'login' &&
                $request['request']['token'] === $expectedToken &&
                $request['request']['user'] === 'test-user';
        },
    ]);
});

it('throws a RuntimeException if the challenge fails', function () {
    Http::fake([
        'https://ucm.example.com/api' => Http::response(['response' => []], 200),
    ]);

    expect(fn () => LaravelGrandstreamAuth::loginFor(123))
        ->toThrow(RuntimeException::class, 'Challenge failed');
});

it('throws a RuntimeException if the login fails', function () {
    Http::fake([
        'https://ucm.example.com/api' => Http::sequence()
            ->push(['response' => ['challenge' => 'good-challenge']])
            ->push(['response' => []]),
    ]);

    expect(fn () => LaravelGrandstreamAuth::loginFor(123))
        ->toThrow(RuntimeException::class, 'Login failed');
});

it('uses cached cookie and does not log in again', function () {
    $uid = 456;
    Cache::put("ucm_cookie:{$uid}", 'cached-cookie', now()->addMinutes(10));

    Http::fake([
        'https://ucm.example.com/api' => Http::response(['status' => 0, 'response' => ['data' => 'success']], 200),
    ]);

    // Mock authenticated user context
    $user = Mockery::mock('Illuminate\Contracts\Auth\Authenticatable');
    $user->shouldReceive('getAuthIdentifier')->andReturn($uid);
    auth()->login($user);

    $res = LaravelGrandstreamAuth::api('someAction');

    expect($res)->toBe(['status' => 0, 'response' => ['data' => 'success']]);

    Http::assertSent(function ($request) {
        return $request['request']['cookie'] === 'cached-cookie' &&
            $request['request']['action'] === 'someAction';
    });

    // Make sure no login/challenge calls were made
    Http::assertNotSent(function ($request) {
        return $request['request']['action'] === 'challenge';
    });
});

it('retries request after re-authenticating if status is -8, -6, or -37', function ($status) {
    $uid = 789;
    Cache::put("ucm_cookie:{$uid}", 'expired-cookie', now()->addMinutes(10));

    Http::fake([
        'https://ucm.example.com/api' => Http::sequence()
            // First call with expired cookie returns auth error
            ->push(['status' => $status, 'response' => ['error' => 'Expired cookie']], 200)
            // Second and third call for loginFor flow (challenge + login)
            ->push(['response' => ['challenge' => 'new-challenge']], 200)
            ->push(['response' => ['cookie' => 'new-cookie']], 200)
            // Fourth call is the retried api action with new cookie
            ->push(['status' => 0, 'response' => ['data' => 'success']], 200),
    ]);

    $user = Mockery::mock('Illuminate\Contracts\Auth\Authenticatable');
    $user->shouldReceive('getAuthIdentifier')->andReturn($uid);
    auth()->login($user);

    $res = LaravelGrandstreamAuth::api('someAction');

    expect($res)->toBe(['status' => 0, 'response' => ['data' => 'success']]);
    expect(Cache::get("ucm_cookie:{$uid}"))->toBe('new-cookie');

    Http::assertSentInOrder([
        // Initial call using the cached expired cookie
        function ($request) {
            return $request['request']['cookie'] === 'expired-cookie' &&
                $request['request']['action'] === 'someAction';
        },
        // Re-auth flow
        function ($request) {
            return $request['request']['action'] === 'challenge';
        },
        function ($request) {
            return $request['request']['action'] === 'login';
        },
        // Retry call using the fresh cookie
        function ($request) {
            return $request['request']['cookie'] === 'new-cookie' &&
                $request['request']['action'] === 'someAction';
        },
    ]);
})->with([-8, -6, -37]);
