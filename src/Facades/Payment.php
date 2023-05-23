<?php

namespace Unlu\PaymentPackage\Facades;

use Illuminate\Support\Facades\Facade;
use Unlu\PaymentPackage\Enums\PaymentChannel;

/**
 * @method static channel(PaymentChannel $channel)
 * @method static setParams(array $data)
 *
 * @see \Unlu\PaymentPackage\Payment
 */
class Payment extends Facade
{

    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'payment';
    }
}
