<?php

namespace Unlu\PaymentPackage\Tests;

use Unlu\PaymentPackage\PaymentPackageServiceProvider;

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
        return [PaymentPackageServiceProvider::class];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('payment.sipay.credentials', [
            'merchant_key' => 'merchant_key',
            'app_secret' => 'app_secret',
            'app_key' => 'app_key',
            'host' => 'https://provisioning.sipay.com.tr'

        ]);
    }
}
