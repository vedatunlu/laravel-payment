<?php

namespace Unlu\PaymentPackage\Contracts;

interface Recurrable
{
    public function recurringPayment(array $params): IPaymentGatewayResponse;
}
