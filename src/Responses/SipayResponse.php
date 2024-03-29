<?php

namespace Unlu\PaymentPackage\Responses;

use Unlu\PaymentPackage\Abstracts\PaymentGatewayResponse;
use Unlu\PaymentPackage\Exceptions\InvalidResponseException;

class SipayResponse extends PaymentGatewayResponse
{

    /**
     * Get sipay response's status code as int
     *
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->response->json('status_code');
    }

    /**
     * Get sipay response's status description as string
     *
     * @return string
     */
    public function getStatusDescription(): string
    {
        return $this->response->json('status_description');
    }

    /**
     * @return int
     */
    public function getHttpStatusCode(): int
    {
        return $this->response->status();
    }

    /**
     * Check sipay response if success
     *
     * @return bool
     */
    public function isSuccess(): bool
    {
        return ($this->getHttpStatusCode() < 300 && $this->getHttpStatusCode() >= 200)
            || (!is_null($this->response->json('status_code'))
                && in_array($this->response->json('status_code'), [100, 101]));
    }

    /**
     * Get sipay response as html form
     *
     * @return string
     * @throws InvalidResponseException
     */
    public function get3DSForm(): string
    {
        if (!$this->isSuccess() || is_array($this->response->json())) {
            throw new InvalidResponseException('Response is not a html form');
        }

        return $this->response->body();
    }

    /**
     * Get sipay response as array
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->response->json();
    }
}
