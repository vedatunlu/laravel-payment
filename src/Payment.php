<?php

namespace Unlu\PaymentPackage;

use Illuminate\Support\Facades\App;
use Unlu\PaymentPackage\Abstract\PaymentGateway;
use Unlu\PaymentPackage\Exceptions\InvalidGatewayException;
use Unlu\PaymentPackage\Gateways\SipayPaymentGateway;

final class Payment
{
    private static array $gateWays = [
        'sipay' => SipayPaymentGateway::class
    ];

    /**
     * Set online payment channel
     *
     * @param  string  $gateway
     * @return PaymentGateway
     * @throws InvalidGatewayException
     */
    public static function gateway(string $gateway): PaymentGateway
    {
        if (!array_key_exists($gateway, self::$gateWays)) {
            throw new InvalidGatewayException('Invalid gateway');
        }

        return App::make(self::$gateWays[$gateway]);
    }
}
