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

it('can add a paging group', function () {
    Http::fake([
        'https://ucm.example.com/api' => Http::response(['status' => 0]),
    ]);

    // Test with default values for trailing arguments
    $resDefault = Grandstream::addPaginggroup('8000', '1001,1002', 'Office Paging');

    expect($resDefault)->toBe(['status' => 0]);

    Http::assertSent(function ($request) {
        $req = $request['request'];

        return $req['action'] === 'addPaginggroup' &&
            $req['extension'] === '8000' &&
            $req['members'] === '1001,1002' &&
            $req['paginggroup_name'] === 'Office Paging' &&
            $req['number_allowed'] === '1000' &&
            $req['paginggroup_type'] === '1way';
    });

    // Test with custom trailing arguments
    Grandstream::addPaginggroup('8000', '1001,1002', 'Office Paging', '2000', '2way');

    Http::assertSent(function ($request) {
        $req = $request['request'];

        return $req['action'] === 'addPaginggroup' &&
            $req['number_allowed'] === '2000' &&
            $req['paginggroup_type'] === '2way';
    });
});

it('can list paging groups', function () {
    Http::fake([
        'https://ucm.example.com/api' => Http::response(['status' => 0, 'response' => ['paginggroups' => []]]),
    ]);

    $resDefault = Grandstream::listPaginggroup();

    expect($resDefault)->toBe(['status' => 0, 'response' => ['paginggroups' => []]]);

    Http::assertSent(function ($request) {
        $req = $request['request'];

        return $req['action'] === 'listPaginggroup' &&
            $req['page'] === '1' &&
            $req['sidx'] === 'extension' &&
            $req['sord'] === 'asc';
    });

    Grandstream::listPaginggroup('2', 'paginggroup_name', 'desc');

    Http::assertSent(function ($request) {
        $req = $request['request'];

        return $req['action'] === 'listPaginggroup' &&
            $req['page'] === '2' &&
            $req['sidx'] === 'paginggroup_name' &&
            $req['sord'] === 'desc';
    });
});

it('can get a specific paging group', function () {
    Http::fake([
        'https://ucm.example.com/api' => Http::response(['status' => 0]),
    ]);

    $res = Grandstream::getPaginggroup('8000');

    expect($res)->toBe(['status' => 0]);

    Http::assertSent(function ($request) {
        return $request['request']['action'] === 'getPaginggroup' &&
            $request['request']['paginggroup'] === '8000';
    });
});

it('can update a paging group', function () {
    Http::fake([
        'https://ucm.example.com/api' => Http::response(['status' => 0]),
    ]);

    // Test with all arguments
    $resAll = Grandstream::updatePaginggroup('8000', '1001,1002,1003', '2way');

    expect($resAll)->toBe(['status' => 0]);

    Http::assertSent(function ($request) {
        $req = $request['request'];

        return $req['action'] === 'updatePaginggroup' &&
            $req['paginggroup'] === '8000' &&
            $req['members'] === '1001,1002,1003' &&
            $req['paginggroup_type'] === '2way';
    });

    // Test with only members
    Grandstream::updatePaginggroup('8000', '1004');

    Http::assertSent(function ($request) {
        $req = $request['request'];

        return $req['action'] === 'updatePaginggroup' &&
            $req['paginggroup'] === '8000' &&
            $req['members'] === '1004' &&
            ! isset($req['paginggroup_type']);
    });
});

it('can delete a paging group', function () {
    Http::fake([
        'https://ucm.example.com/api' => Http::response(['status' => 0]),
    ]);

    $res = Grandstream::deletePaginggroup('8000');

    expect($res)->toBe(['status' => 0]);

    Http::assertSent(function ($request) {
        return $request['request']['action'] === 'deletePaginggroup' &&
            $request['request']['paginggroup'] === '8000';
    });
});

it('can initiate multicast paging', function () {
    Http::fake([
        'https://ucm.example.com/api' => Http::response(['status' => 0]),
    ]);

    $res = Grandstream::multicastPaging('1001', '8000');

    expect($res)->toBe(['status' => 0]);

    Http::assertSent(function ($request) {
        return $request['request']['action'] === 'MulticastPaging' &&
            $request['request']['caller'] === '1001' &&
            $request['request']['pagingnum'] === '8000';
    });
});

it('can hang up multicast paging', function () {
    Http::fake([
        'https://ucm.example.com/api' => Http::response(['status' => 0]),
    ]);

    $res = Grandstream::multicastPagingHangup('8000');

    expect($res)->toBe(['status' => 0]);

    Http::assertSent(function ($request) {
        return $request['request']['action'] === 'MulticastPagingHangup' &&
            $request['request']['pagingnum'] === '8000';
    });
});
