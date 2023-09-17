<?php

namespace Unlu\PaymentPackage\Contracts;

interface IPaymentGatewayValidation
{
    public function validateHashKey(string $hashKey): mixed;
}
