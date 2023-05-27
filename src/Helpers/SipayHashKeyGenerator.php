<?php

namespace Unlu\PaymentPackage\Helpers;

use Unlu\PaymentPackage\Payloads\SipayPayload;

final class SipayHashKeyGenerator
{
    /**
     * @var string
     */
    private string $merchantKey;

    /**
     * @var string
     */
    private string $appSecret;

    public function __construct(string $merchantKey, string $appSecret)
    {
        $this->merchantKey = $merchantKey;
        $this->appSecret = $appSecret;
    }

    /**
     * Generate hash key for payment
     *
     * @param  SipayPayload  $payload
     * @return string
     */
    public function paymentHashKey(SipayPayload $payload): string {
        $str = $payload->getData('total').'|'.$payload->getData('installments_number')
            .'|'.$payload->getData('currency_code').'|'.$this->merchantKey.'|'.$payload->getData('invoice_id');

        return $this->stringToHashedKey($str);
    }

    /**
     * Generate hash key for saving credit cards
     *
     * @param  SipayPayload  $payload
     * @return string
     */
    public function saveCardHashKey(SipayPayload $payload): string {
        $str = $this->merchantKey.'|'.$payload->getData('customer_number').'|'.$payload->getData('card_holder_name')
            .'|'.$payload->getData('card_number').'|'.$payload->getData('expiry_month').'|'.$payload->getData('expiry_year');

        return $this->stringToHashedKey($str);
    }

    /**
     * Generate hash key for updating credit cards
     *
     * @param  SipayPayload  $payload
     * @return string
     */
    public function storedCardHashKey(SipayPayload $payload): string {
        $str = $this->merchantKey.'|'.$payload->getData('customer_number').'|'.$payload->getData('card_token');
        return $this->stringToHashedKey($str);
    }

    /**
     * Generate hash key for refund
     *
     * @param  SipayPayload $payload
     * @return string
     */
    public function refundHashKey(SipayPayload $payload): string {
        $str = $payload->getData('amount').'|'.$payload->getData('invoice_id').'|'.$this->merchantKey;
        return $this->stringToHashedKey($str);
    }

    /**
     * @param string $str
     * @return string
     */
    protected function stringToHashedKey(string $str): string
    {
        $iv = substr(sha1((string)mt_rand()), 0, 16);
        $password = sha1($this->appSecret);
        $salt = substr(sha1((string)mt_rand()), 0, 4);
        $saltWithPassword = hash('sha256', $password . $salt);
        $encrypted = openssl_encrypt("$str", 'aes-256-cbc', "$saltWithPassword", null, $iv);
        $msgEncryptedBundle = "$iv:$salt:$encrypted";

        return str_replace('/', '__', $msgEncryptedBundle);
    }
}
