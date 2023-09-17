<?php

namespace Unlu\PaymentPackage\Contracts;

interface NonSecurePayable
{
    public function payWith2D(array $params): IPaymentGatewayResponse;

    public function verifyPayment(array $params): IPaymentGatewayResponse;

    public function transactionStatus(array $params): IPaymentGatewayResponse;
}
