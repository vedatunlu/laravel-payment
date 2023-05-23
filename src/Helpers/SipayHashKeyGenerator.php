<?php

namespace Unlu\PaymentPackage\Helpers;

/**
 *
 */
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

    public function __construct()
    {
        $this->merchantKey = config('sipay.credentials.merchant_key');
        $this->appSecret = config('sipay.credentials.app_secret');
    }

    /**
     * Generate hash key for payment
     *
     * @param  float  $total
     * @param  int  $installment
     * @param  string  $currency_code
     * @param  string  $invoice_id
     * @return string
     */
    public function generatePaymentHashKey(float $total, int $installment, string $currency_code, string $invoice_id,): string {
        $data = $total.'|'.$installment.'|'.$currency_code.'|'.$this->merchantKey.'|'.$invoice_id;
        $iv = substr(sha1((string) mt_rand()), 0, 16);
        $password = sha1($this->appSecret);
        $salt = substr(sha1((string) mt_rand()), 0, 4);
        $saltWithPassword = hash('sha256', $password.$salt);
        $encrypted = openssl_encrypt("$data", 'aes-256-cbc', "$saltWithPassword", null, $iv);
        $msgEncryptedBundle = "$iv:$salt:$encrypted";

        return str_replace('/', '__', $msgEncryptedBundle);
    }

    /**
     * Generate hash key for saving credit cards
     *
     * @param  int  $customerNumber
     * @param  int  $cardNumber
     * @param  string  $cardHolderName
     * @param  int  $expiryMonth
     * @param  int  $expiryYear
     * @return string
     */
    public function generateSaveCardHashKey(int $customerNumber, int $cardNumber, string $cardHolderName, int $expiryMonth, int $expiryYear,): string {
        $data = $this->merchantKey.'|'.$customerNumber.'|'.$cardHolderName.'|'.$cardNumber.'|'.$expiryMonth.'|'.$expiryYear;
        $iv = substr(sha1((string) mt_rand()), 0, 16);
        $password = sha1($this->appSecret);
        $salt = substr(sha1((string) mt_rand()), 0, 4);
        $saltWithPassword = hash('sha256', $password.$salt);
        $encrypted = openssl_encrypt("$data", 'aes-256-cbc', "$saltWithPassword", 0, $iv);
        $msgEncryptedBundle = "$iv:$salt:$encrypted";

        return str_replace('/', '__', $msgEncryptedBundle);
    }

    /**
     * Generate hash key for updating credit cards
     *
     * @param  int  $customerNumber
     * @param  string  $cardToken
     * @return string
     */
    public function generateUpdateCardHashKey(int $customerNumber, string $cardToken): string {
        $data = $this->merchantKey.'|'.$customerNumber.'|'.$cardToken;
        $iv = substr(sha1(mt_rand()), 0, 16);
        $password = sha1($this->appSecret);
        $salt = substr(sha1(mt_rand()), 0, 4);
        $saltWithPassword = hash('sha256', $password.$salt);
        $encrypted = openssl_encrypt("$data", 'aes-256-cbc', "$saltWithPassword", null, $iv);
        $msg_encrypted_bundle = "$iv:$salt:$encrypted";
        $msg_encrypted_bundle = str_replace('/', '__', $msg_encrypted_bundle);

        return $msg_encrypted_bundle;
    }

    /**
     * Generate hash key for deleting credit cards
     *
     * @param  int  $customerNumber
     * @param  string  $cardToken
     * @return string
     */
    public function generateDeleteCardHashKey(int $customerNumber, string $cardToken,): string {
        $data = $this->merchantKey.'|'.$customerNumber.'|'.$cardToken;
        $iv = substr(sha1((string) mt_rand()), 0, 16);
        $password = sha1($this->appSecret);
        $salt = substr(sha1((string) mt_rand()), 0, 4);
        $saltWithPassword = hash('sha256', $password.$salt);
        $encrypted = openssl_encrypt("$data", 'aes-256-cbc', "$saltWithPassword", 0, $iv);
        $msg_encrypted_bundle = "$iv:$salt:$encrypted";

        return str_replace('/', '__', $msg_encrypted_bundle);
    }

    /**
     * Generate hash key for refund
     *
     * @param  float  $amount
     * @param  string  $invoiceId
     * @return string
     */
    public function generateRefundHashKey(float $amount, string $invoiceId,): string {
        $data = $amount.'|'.$invoiceId.'|'.$this->merchantKey;
        $iv = substr(sha1((string) mt_rand()), 0, 16);
        $password = sha1($this->appSecret);
        $salt = substr(sha1((string) mt_rand()), 0, 4);
        $saltWithPassword = hash('sha256', $password.$salt);
        $encrypted = openssl_encrypt(
            "$data", 'aes-256-cbc', "$saltWithPassword", 0, $iv
        );
        $msg_encrypted_bundle = "$iv:$salt:$encrypted";
        $hash_key = str_replace('/', '__', $msg_encrypted_bundle);

        return $hash_key;
    }
}
