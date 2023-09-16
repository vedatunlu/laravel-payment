<?php

namespace Unlu\PaymentPackage\Contracts;

interface NonSecurePayable
{
    public function payWith2D(array $params): PaymentGatewayResponse;

    public function verifyPayment(array $params): PaymentGatewayResponse;

    public function transactionStatus(array $params): PaymentGatewayResponse;
}
