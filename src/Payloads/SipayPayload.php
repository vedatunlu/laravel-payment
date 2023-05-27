<?php

namespace Unlu\PaymentPackage\Payloads;

use Unlu\PaymentPackage\Contracts\PaymentPayload;
use Unlu\PaymentPackage\Helpers\SipayHashKeyGenerator;

class SipayPayload implements PaymentPayload
{

    /**
     * @var array
     */
    protected array $data;

    /**
     * @var string
     */
    protected string $merchantKey;

    /**
     * @var SipayHashKeyGenerator
     */
    protected SipayHashKeyGenerator $hashKeyGenerator;

    public function __construct(SipayHashKeyGenerator $hashKeyGenerator, $merchantKey)
    {
        $this->hashKeyGenerator = $hashKeyGenerator;
        $this->merchantKey = $merchantKey;
        $this->addData('merchant_key', $this->merchantKey);
    }

    public function setData(array $data = []): self
    {
        $this->data = array_merge($data, $this->data);
        return $this;
    }

    public function getData(string $key): mixed
    {
        return $this->data[$key] ?? null;
    }

    /**
     * Adds new data to the payload.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return self
     */
    public function addData(string $key, mixed $value): self
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * Removes data from the payload.
     *
     * @param  string  $key
     * @return self
     */
    public function removeData(string $key): self
    {
        unset($this->data[$key]);
        return $this;
    }

    /**
     * @param string $type
     * @return self
     */
    public function addHashKey(string $type): self
    {
        $hashKey = match ($type) {
            'payment' => $this->hashKeyGenerator->paymentHashKey($this),
            'saveCard' => $this->hashKeyGenerator->saveCardHashKey($this),
            'updateCard', 'deleteCard' => $this->hashKeyGenerator->storedCardHashKey($this),
            'refund' => $this->hashKeyGenerator->refundHashKey($this),
            'verification' => $this->hashKeyGenerator->paymentVerificationHashKey($this),
            'transaction' => $this->hashKeyGenerator->transactionStatusHashKey($this)
        };

        $this->addData('hash_key', $hashKey);

        return $this;
    }
    /**
     * Returns payload data as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->data;
    }
}
