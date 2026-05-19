<?php

use Illuminate\Support\Facades\Artisan;

it('can run the console command', function () {
    Artisan::call('laravel-grandstream');

    $output = Artisan::output();

    expect($output)->toContain('All done');
});
