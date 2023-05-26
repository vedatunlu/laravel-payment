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
        $this->mergeConfigFrom(__DIR__.'/../config/payment.php', 'payment');
        $this->app->bind(SipayPayload::class, function (Application $app) {
            $merchantKey = config('payment.sipay.credentials.merchant_key');
            return new SipayPayload($app->make(SipayHashKeyGenerator::class), $merchantKey);
        });
        $this->app->bind(SipayPaymentGateway::class, function (Application $app) {
            $appSecret = config('payment.sipay.credentials.app_secret');
            $appKey = config('payment.sipay.credentials.app_key');
            $client = Http::withOptions([
                'base_uri' => config('payment.sipay.credentials.host')
            ]);
            return new SipayPaymentGateway($app->make(SipayPayload::class), $client, $appSecret, $appKey);
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
                __DIR__.'/../config/payment.php' => config_path('payment.php')
            ], 'payment-config');
        }
    }
}
