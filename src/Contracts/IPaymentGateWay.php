<?php

namespace Unlu\PaymentPackage\Contracts;

interface IPaymentGateWay
{
    public function payWith3D(array $params): IPaymentGatewayResponse;
}
