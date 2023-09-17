<?php

namespace Unlu\PaymentPackage\Contracts;

interface IPaymentPayload
{
    public function setData(array $data = []): self;

    public function getData(string $ket): mixed;

    public function addData(string $key, mixed $value): self;

    public function removeData(string $key): self;

    public function toArray(): array;
}
