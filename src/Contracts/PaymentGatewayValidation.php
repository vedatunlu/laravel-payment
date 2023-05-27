<?php

namespace Unlu\PaymentPackage\Contracts;

interface PaymentGatewayValidation
{
    public function validateHashKey(string $hashKey): mixed;
}
