<?php

namespace Unlu\PaymentPackage\Contracts;

interface PaymentGatewayResponse
{
    public function getHttpStatusCode(): int;

    public function isSuccess(): bool;

    public function get3DSForm(): string;

    public function toArray(): array;
}
