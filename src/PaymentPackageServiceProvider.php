<?php

namespace Unlu\PaymentPackage;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Foundation\Application;
use Unlu\PaymentPackage\Gateways\SipayPaymentGateway;
use Unlu\PaymentPackage\Helpers\SipayHashKeyGenerator;
use Unlu\PaymentPackage\Payloads\SipayPayload;

class PaymentPackageServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/sipay.php', 'sipay');
        $this->app->bind(SipayPayload::class, function (Application $app) {
            return new SipayPayload($app->make(SipayHashKeyGenerator::class));
        });
        $this->app->bind(SipayPaymentGateway::class, function (Application $app) {
            $client = Http::withOptions([
                'base_uri' => config('sipay.credentials.host')
            ]);
            return new SipayPaymentGateway($app->make(SipayPayload::class), $client);
        });
    }

    /**
     *
     * @return void
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/sipay.php' => config_path('sipay.php')
            ], 'config');
        }
    }
}
