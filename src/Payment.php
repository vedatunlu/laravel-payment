<?php

namespace Unlu\PaymentPackage;

use Illuminate\Support\Facades\App;
use Unlu\PaymentPackage\Abstract\PaymentGateway;
use Unlu\PaymentPackage\Contracts\PaymentGatewayValidation;
use Unlu\PaymentPackage\Exceptions\InvalidGatewayException;
use Unlu\PaymentPackage\Exceptions\InvalidGatewayValidatorException;
use Unlu\PaymentPackage\Gateways\SipayPaymentGateway;
use Unlu\PaymentPackage\Helpers\SipayHashKeyValidator;

final class Payment
{
    private static array $gateWays = [
        'sipay' => SipayPaymentGateway::class
    ];

    /**
     * @var array|string[]
     */
    private static array $validators = [
        'sipay' => SipayHashKeyValidator::class
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
        self::gateWayExists($gateway);
        return App::make(self::$gateWays[$gateway]);
    }

    /**
     * @param string $gateway
     * @param $hashKey
     * @return mixed
     * @throws InvalidGatewayValidatorException
     */
    public static function validate(string $gateway, $hashKey): mixed
    {
        self::validatorExists($gateway);
        return App::make(self::$validators[$gateway])->validateHashKey($hashKey);
    }

    private static function gateWayExists(string $gateway): void
    {
        array_key_exists($gateway, self::$gateWays)
            ?: throw new InvalidGatewayException('Invalid gateway');
    }

    private static function validatorExists(string $gateway): void
    {
        array_key_exists($gateway, self::$validators)
            ?: throw new InvalidGatewayValidatorException('Gateway has no validator');
    }
}
