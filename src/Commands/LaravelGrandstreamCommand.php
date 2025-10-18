<?php

namespace AkramGhaleb\LaravelGrandstream\Commands;

use Illuminate\Console\Command;

class LaravelGrandstreamCommand extends Command
{
    public $signature = 'laravel-grandstream';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
