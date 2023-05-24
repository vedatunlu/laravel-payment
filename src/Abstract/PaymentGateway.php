<?php

namespace Unlu\PaymentPackage\Abstract;

use Unlu\PaymentPackage\Contracts\PaymentGatewayResponse;

abstract class PaymentGateway
{
    protected string $authToken;

    public function __construct()
    {
        $this->authToken = $this->getAuthToken();
    }

    protected abstract function getAuthToken(): string;

    public abstract function payWith3D(array $params): PaymentGatewayResponse;
}
