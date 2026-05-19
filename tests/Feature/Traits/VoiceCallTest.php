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

    Http::fake([
        'https://ucm.example.com/api' => Http::response(['status' => 0, 'response' => ['data' => 'success']]),
    ]);
});

it('can list bridged and unbridged channels', function () {
    Grandstream::listBridgedChannels();
    Http::assertSent(fn ($request) => $request['request']['action'] === 'listBridgedChannels');

    Grandstream::listUnBridgedChannels();
    Http::assertSent(fn ($request) => $request['request']['action'] === 'listUnBridgedChannels');
});

it('can perform basic call operations (hangup, barge, mute, unmute, hold, unhold, accept, refuse)', function () {
    Grandstream::hangup('SIP/1001-abc');
    Http::assertSent(fn ($request) => $request['request']['action'] === 'Hangup' && $request['request']['channel'] === 'SIP/1001-abc');

    Grandstream::callbarge('1000', 'SIP/1001-abc', '1002');
    Http::assertSent(fn ($request) => $request['request']['action'] === 'callbarge' &&
        $request['request']['barge-exten'] === '1000' &&
        $request['request']['channel'] === 'SIP/1001-abc' &&
        $request['request']['exten'] === '1002');

    Grandstream::mute('SIP/1001-abc');
    Http::assertSent(fn ($request) => $request['request']['action'] === 'mute' && $request['request']['channel'] === 'SIP/1001-abc');

    Grandstream::unmute('SIP/1001-abc');
    Http::assertSent(fn ($request) => $request['request']['action'] === 'unmute' && $request['request']['channel'] === 'SIP/1001-abc');

    Grandstream::hold('SIP/1001-abc');
    Http::assertSent(fn ($request) => $request['request']['action'] === 'hold' && $request['request']['channel'] === 'SIP/1001-abc');

    Grandstream::unhold('SIP/1001-abc');
    Http::assertSent(fn ($request) => $request['request']['action'] === 'unhold' && $request['request']['channel'] === 'SIP/1001-abc');

    Grandstream::acceptCall('SIP/1001-abc');
    Http::assertSent(fn ($request) => $request['request']['action'] === 'acceptCall' && $request['request']['channel'] === 'SIP/1001-abc');

    Grandstream::refuseCall('SIP/1001-abc');
    Http::assertSent(fn ($request) => $request['request']['action'] === 'refuseCall' && $request['request']['channel'] === 'SIP/1001-abc');
});

it('can dial extensions, outbound, IVRs, queues, and ring groups', function () {
    Grandstream::dialExtension('1002', '1001');
    Http::assertSent(fn ($request) => $request['request']['action'] === 'dialExtension' &&
        $request['request']['callee'] === '1002' &&
        $request['request']['caller'] === '1001');

    Grandstream::dialOutbound('0551234567', '1001');
    Http::assertSent(fn ($request) => $request['request']['action'] === 'dialOutbound' &&
        $request['request']['outbound'] === '0551234567' &&
        $request['request']['caller'] === '1001');

    Grandstream::dialIVR('1001', '7000');
    Http::assertSent(fn ($request) => $request['request']['action'] === 'dialIVR' &&
        $request['request']['caller'] === '1001' &&
        $request['request']['ivrnumber'] === '7000');

    Grandstream::dialIVROutbound('0551234567', '7000');
    Http::assertSent(fn ($request) => $request['request']['action'] === 'dialIVROutbound' &&
        $request['request']['outcaller'] === '0551234567' &&
        $request['request']['ivrnumber'] === '7000');

    Grandstream::dialQueue('1001', '6000');
    Http::assertSent(fn ($request) => $request['request']['action'] === 'dialQueue' &&
        $request['request']['outcaller'] === '1001' &&
        $request['request']['queue'] === '6000');

    Grandstream::dialRinggroup('1001', '5000');
    Http::assertSent(fn ($request) => $request['request']['action'] === 'dialRinggroup' &&
        $request['request']['outcaller'] === '1001' &&
        $request['request']['ringgroup'] === '5000');

    Grandstream::dialOutboundTwo('0551234567', '0557654321');
    Http::assertSent(fn ($request) => $request['request']['action'] === 'dialOutboundTwo' &&
        $request['request']['outcaller'] === '0551234567' &&
        $request['request']['outcallee'] === '0557654321');
});

it('can transfer active, inbound, and outbound calls', function () {
    Grandstream::callTransfer('SIP/1001-abc', '1002');
    Http::assertSent(fn ($request) => $request['request']['action'] === 'callTransfer' &&
        $request['request']['channel'] === 'SIP/1001-abc' &&
        $request['request']['extension'] === '1002');

    Grandstream::transferNumberInbound('SIP/1001-abc', '1002');
    Http::assertSent(fn ($request) => $request['request']['action'] === 'transferNumberInbound' &&
        $request['request']['channel'] === 'SIP/1001-abc' &&
        $request['request']['callee'] === '1002');

    Grandstream::transferNumberOutbound('SIP/1001-abc', '0551234567');
    Http::assertSent(fn ($request) => $request['request']['action'] === 'transferNumberOutbound' &&
        $request['request']['channel'] === 'SIP/1001-abc' &&
        $request['request']['outbound'] === '0551234567');
});
