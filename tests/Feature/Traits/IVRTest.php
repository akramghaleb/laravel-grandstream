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

it('can list IVRs', function () {
    Http::fake([
        'https://ucm.example.com/api' => Http::response([
            'status' => 0,
            'response' => ['ivrs' => [['ivr' => '7000', 'ivr_name' => 'Main IVR']]],
        ]),
    ]);

    $res = Grandstream::listIVR();

    expect($res)->toBe([
        'status' => 0,
        'response' => ['ivrs' => [['ivr' => '7000', 'ivr_name' => 'Main IVR']]],
    ]);

    Http::assertSent(function ($request) {
        return $request['request']['action'] === 'listIVR';
    });
});

it('can get a single IVR details', function () {
    Http::fake([
        'https://ucm.example.com/api' => Http::response([
            'status' => 0,
            'response' => ['ivr' => '7000', 'ivr_name' => 'Main IVR'],
        ]),
    ]);

    $res = Grandstream::getIVR('7000');

    expect($res)->toBe([
        'status' => 0,
        'response' => ['ivr' => '7000', 'ivr_name' => 'Main IVR'],
    ]);

    Http::assertSent(function ($request) {
        return $request['request']['action'] === 'getIVR' &&
            $request['request']['ivr'] === '7000';
    });
});

it('can delete an IVR', function () {
    Http::fake([
        'https://ucm.example.com/api' => Http::response([
            'status' => 0,
        ]),
    ]);

    $res = Grandstream::deleteIVR('7000');

    expect($res)->toBe([
        'status' => 0,
    ]);

    Http::assertSent(function ($request) {
        return $request['request']['action'] === 'deleteIVR' &&
            $request['request']['ivr'] === '7000';
    });
});

it('can add IVR with defaults and option normalization', function () {
    Http::fake([
        'https://ucm.example.com/api' => Http::response([
            'status' => 0,
        ]),
    ]);

    $members = [
        ['keypress' => '1', 'keypress_event' => 'dial_extension', 'member_prompt' => '1001'],
        ['keypress' => '', 'keypress_event' => 'dial_extension'], // Should be skipped due to empty keypress
        ['keypress' => '2'], // Should fill defaults keypress_event ('member_prompt') and member_prompt ('goodbye')
    ];

    $res = Grandstream::addIVR('7000', 'Main IVR', 'welcome-sound', [
        'dial_conference' => true,      // boolean true -> yes
        'dial_directory' => 'false',     // string false -> no
        'dial_extension' => 0,          // should not change or could be normalized (let's keep 0 as is)
        'dial_fax' => 'y',              // 'y' -> yes
        'dial_paginggroup' => '1',      // '1' -> yes
        'dial_queue' => 'n',            // 'n' -> no
        'members' => $members,
    ]);

    expect($res)->toBe([
        'status' => 0,
    ]);

    Http::assertSent(function ($request) {
        $req = $request['request'];

        // Assert merged and normalized fields
        return $req['action'] === 'addIVR' &&
            $req['extension'] === '7000' &&
            $req['ivr_name'] === 'Main IVR' &&
            $req['welcome_prompt'] === 'welcome-sound' &&
            $req['dial_conference'] === 'yes' &&
            $req['dial_directory'] === 'no' &&
            $req['dial_fax'] === 'yes' &&
            $req['dial_paginggroup'] === 'yes' &&
            $req['dial_queue'] === 'no' &&
            // Assert default values preserved
            $req['alertinfo'] === 'ring1' &&
            $req['replace_caller_id'] === 'yes' &&
            // Assert members list normalization
            count($req['members']) === 2 &&
            $req['members'][0] === [
                'keypress' => '1',
                'keypress_event' => 'dial_extension',
                'member_prompt' => '1001',
            ] &&
            $req['members'][1] === [
                'keypress' => '2',
                'keypress_event' => 'member_prompt',
                'member_prompt' => 'goodbye',
            ];
    });
});

it('can update IVR with normalized options', function () {
    Http::fake([
        'https://ucm.example.com/api' => Http::response([
            'status' => 0,
        ]),
    ]);

    $res = Grandstream::updateIVR('7000', [
        'dial_conference' => true,
        'dial_directory' => 'false',
        'replace_caller_id' => '1',
        'switch' => false,
        'ivr_name' => 'Updated Name',
    ]);

    expect($res)->toBe([
        'status' => 0,
    ]);

    Http::assertSent(function ($request) {
        $req = $request['request'];

        return $req['action'] === 'updateIVR' &&
            $req['ivr'] === '7000' &&
            $req['dial_conference'] === 'yes' &&
            $req['dial_directory'] === 'no' &&
            $req['replace_caller_id'] === 'yes' &&
            $req['switch'] === 'no' &&
            $req['ivr_name'] === 'Updated Name';
    });
});

it('returns an error if updating IVR with no fields', function () {
    $res = Grandstream::updateIVR('7000', []);

    expect($res)->toBe([
        'status' => -1,
        'response' => ['error' => 'No fields to update'],
    ]);

    Http::assertNotSent(function ($request) {
        return $request['request']['action'] === 'updateIVR';
    });
});
