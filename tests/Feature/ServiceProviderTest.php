<?php

use AkramGhaleb\LaravelGrandstream\Facades\Grandstream;
use AkramGhaleb\LaravelGrandstream\LaravelGrandstream;
use Illuminate\Support\Facades\Artisan;

it('merges and registers the configuration', function () {
    expect(config('grandstream'))->toBeArray()
        ->and(config('grandstream.base'))->toBe('http://127.0.0.1:8089')
        ->and(config('grandstream.user'))->toBe('admin')
        ->and(config('grandstream.version'))->toBe('1.2')
        ->and(config('grandstream.cookie_ttl'))->toBe(9);
});

it('registers the grandstream facade', function () {
    $resolved = app(Grandstream::getFacadeRoot()::class);
    expect($resolved)->toBeInstanceOf(LaravelGrandstream::class);
});

it('registers the console command', function () {
    $commands = Artisan::all();
    expect(array_key_exists('laravel-grandstream', $commands))->toBeTrue();
});
