<?php

namespace Unlu\PaymentPackage\Abstracts;

use Unlu\PaymentPackage\Contracts\PaymentGatewayResponse;

abstract class PaymentGateway
{
    protected string $authToken;

    public function __construct()
    {
        $this->authToken = $this->getAuthToken();
    }

    abstract protected function getAuthToken(): string;

    abstract public function payWith3D(array $params): PaymentGatewayResponse;
}
