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
    }

    public function setData(array $data = []): self
    {
        $this->data = $data;
        $this->addData('merchant_key', $this->merchantKey);
        return $this;
    }

    protected function getData(string $key): mixed
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

    public function addSaveCardHashKey(): self
    {
        $this->data['hash_key'] = $this->hashKeyGenerator->generateSaveCardHashKey(
            $this->getData('customer_number'),
            $this->getData('card_number'),
            $this->getData('card_holder_name'),
            $this->getData('expiry_month'),
            $this->getData('expiry_year'),
        );

        return $this;
    }

    public function addUpdateCardHashKey(): self
    {
        $this->data['hash_key'] = $this->hashKeyGenerator->generateUpdateCardHashKey(
            $this->getData('customer_number'), $this->getData('card_token')
        );
        return $this;
    }

    public function addDeleteCardHashKey(): self
    {
        $this->data['hash_key'] = $this->hashKeyGenerator->generateDeleteCardHashKey(
            $this->getData('customer_number'),
            $this->getData('card_token'),
        );

        return $this;
    }

    public function addPaymentHashKey(): self
    {
        $this->data['hash_key'] = $this->hashKeyGenerator->generatePaymentHashKey(
            $this->getData('total'),
            $this->getData('installments_number'),
            $this->getData('currency_code'),
            $this->getData('invoice_id'),
        );

        return $this;
    }

    public function addRefundHashKey(): self
    {
        $this->data['hash_key'] = $this->hashKeyGenerator->generateRefundHashKey(
            $this->getData('amount'), $this->getData('invoice_id'),
        );

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
