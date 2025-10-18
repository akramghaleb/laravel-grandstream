<?php

namespace AkramGhaleb\LaravelGrandstream;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use AkramGhaleb\LaravelGrandstream\Commands\LaravelGrandstreamCommand;

class LaravelGrandstreamServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-grandstream')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel_grandstream_table')
            ->hasCommand(LaravelGrandstreamCommand::class);
    }
}
