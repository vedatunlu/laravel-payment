<?php

namespace Unlu\PaymentPackage\Contracts;

interface PaymentPayload
{

    /**
     * @return array
     */
    public function toArray(): array;

    /**
     * @param  array<string, mixed>  $data
     * @return $this
     */
    public function setData(array $data): self;

    /**
     * @param  string  $key
     * @param  mixed<string, mixed>  $value
     * @return $this
     */
    public function addData(string $key, mixed $value): self;

    /**
     * @param  string  $key
     * @return $this
     */
    public function removeData(string $key): self;
}
