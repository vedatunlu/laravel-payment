<?php

namespace Unlu\PaymentPackage\Contracts;

interface PaymentPayload
{

    public function toArray(): array;

    public function setData(array $data = []): self;

    public function addData(string $key, mixed $value): self;

    public function removeData(string $key): self;
}
