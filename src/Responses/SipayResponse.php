<?php

namespace Unlu\PaymentPackage\Responses;

use Unlu\PaymentPackage\Contracts\PaymentGatewayResponse;
use Unlu\PaymentPackage\Exceptions\InvalidResponseException;

class SipayResponse implements PaymentGatewayResponse
{
    /**
     * @var array|string
     */
    protected array|string $data;

    /**
     * @var int
     */
    public int $httpStatusCode;

    /**
     * Initialize object class with i
     *
     * @param  array|string  $data
     * @param  int  $httpStatusCode
     */
    public function __construct(array|string $data, int $httpStatusCode)
    {
        $this->data = $data;
        $this->httpStatusCode = $httpStatusCode;
    }

    /**
     * Get sipay response's status code as int
     *
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->data['status_code'];
    }

    /**
     * Get sipay response's status description as string
     *
     * @return string
     */
    public function getStatusDescription(): string
    {
        return $this->data['status_description'];
    }

    /**
     * @return int
     */
    public function getHttpStatusCode(): int
    {
        return $this->httpStatusCode;
    }

    /**
     * Check sipay response if success
     *
     * @return bool
     */
    public function isSuccess(): bool
    {
        return ($this->data['status_code'] === 100 || $this->data['status_code'] === 101)
            && ($this->httpStatusCode < 300 && $this->httpStatusCode >= 200);
    }

    /**
     * Get sipay response as html form
     *
     * @return string
     * @throws InvalidResponseException
     */
    public function get3DSForm(): string
    {
        if (is_array($this->data)) {
            throw new InvalidResponseException('Response is not a html form');
        }

        return $this->data;
    }

    /**
     * Get sipay response as array
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->data;
    }
}
