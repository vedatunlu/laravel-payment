<?php

namespace Unlu\PaymentPackage\Helpers;

final class SipayHashKeyValidator
{

    /**
     * Validate incoming hash key
     *
     * @param  string  $hashKey
     * @param  string  $appSecret
     * @return array|false
     */
    public static function validateHashKey(string $hashKey, string $appSecret): array|false
    {
        $status = $currencyCode = "";
        $total = $invoiceId = $orderId = 0;

        if (!empty($hashKey)) {
            $hashKey = str_replace('__', '/', $hashKey);
            $password = sha1($appSecret);

            $components = explode(':', $hashKey);
            if (count($components) > 2) {
                $iv = isset($components[0]) ? $components[0] : "";
                $salt = isset($components[1]) ? $components[1] : "";
                $salt = hash('sha256', $password.$salt);
                $encryptedMsg = isset($components[2]) ? $components[2] : "";
                $decryptedMsg = openssl_decrypt($encryptedMsg, 'aes-256-cbc', $salt, 0, $iv);

                if (strpos($decryptedMsg, '|') !== false) {
                    $array = explode('|', $decryptedMsg);
                    $status = isset($array[0]) ? $array[0] : 0;
                    $total = isset($array[1]) ? $array[1] : 0;
                    $invoiceId = isset($array[2]) ? $array[2] : '0';
                    $orderId = isset($array[3]) ? $array[3] : 0;
                    $currencyCode = isset($array[4]) ? $array[4] : '';

                    return [
                        'status' => $status ?? null,
                        'total' => $total ?? null,
                        'invoiceID' => $invoiceId ?? null,
                        'orderID' => $orderId ?? null,
                        'currencyCode' => $currencyCode ?? null
                    ];

                }
            }
        }

        return false;
    }
}
