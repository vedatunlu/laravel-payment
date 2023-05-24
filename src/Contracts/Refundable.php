<?php

namespace Unlu\PaymentPackage\Contracts;

interface Refundable
{
    public function refund(array $params): PaymentGatewayResponse;
}
