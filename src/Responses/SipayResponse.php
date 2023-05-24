<?php

namespace Unlu\PaymentPackage\Responses;

use Illuminate\Http\Client\Response;
use Unlu\PaymentPackage\Contracts\PaymentGatewayResponse;
use Unlu\PaymentPackage\Exceptions\InvalidResponseException;

class SipayResponse implements PaymentGatewayResponse
{
    /**
     * @var Response
     */
    protected mixed $response;

    /**
     * @var int
     */
    public int $httpStatusCode;

    public function __construct(Response $response)
    {
        $this->response = $response;
    }

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
        return ($this->response->json('status_code') === 100 || $this->response->json('status_code') === 101)
            && ($this->getHttpStatusCode() < 300 && $this->getStatusCode() >= 200);
    }

    /**
     * Get sipay response as html form
     *
     * @return string
     * @throws InvalidResponseException
     */
    public function get3DSForm(): string
    {
        if (is_array($this->response->json())) {
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
