<?php

namespace Unlu\PaymentPackage\Tests;

class TestCase extends \Orchestra\Testbench\TestCase
{

    /**
     * Get package providers.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array<int, class-string<\Illuminate\Support\ServiceProvider>>
     */
    protected function getPackageProviders($app): array
    {
        return [
            'Unlu\PaymentPackage\PaymentPackageServiceProvider',
        ];
    }
}
