<?php

namespace Unlu\PaymentPackage\Payloads;


use Unlu\PaymentPackage\Abstracts\PaymentPayload;
use Unlu\PaymentPackage\Helpers\SipayHashKeyGenerator;

class SipayPayload extends PaymentPayload
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
            'refund' => $this->hashKeyGenerator->refundHashKey($this)
        };

        $this->addData('hash_key', $hashKey);

        return $this;
    }
}
