<?php

namespace Unlu\PaymentPackage\Abstracts;

abstract class PaymentPayload
{

    /**
     * @var array
     */
    protected array $data;

    /**
     * @param array $data
     * @return self
     */
    public function setData(array $data = []): self
    {
        $this->data = array_merge($data, $this->data);
        return $this;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getData(string $key): mixed
    {
        return $this->data[$key] ?? null;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function addData(string $key, mixed $value): self
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * @param string $key
     * @return $this
     */
    public function removeData(string $key): self
    {
        unset($this->data[$key]);
        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->data;
    }
}
