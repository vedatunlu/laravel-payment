<?php

namespace Unlu\PaymentPackage\Contracts;

interface Installable
{
    public function installmentInquiry(array $params): PaymentGatewayResponse;
}
