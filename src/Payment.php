<?php

namespace Unlu\PaymentPackage;

use Illuminate\Support\Facades\App;
use Unlu\PaymentPackage\Contracts\PaymentGateway;
use Unlu\PaymentPackage\Exceptions\InvalidChannelNameException;
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
     * @throws InvalidChannelNameException
     */
    public static function gateway(string $gateway): PaymentGateway
    {
        if (!array_key_exists($gateway, self::$gateWays)) {
            throw new InvalidChannelNameException('Invalid gateway');
        }

        return App::make(self::$gateWays[$gateway]);
    }
}
