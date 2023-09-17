<?php

namespace Unlu\PaymentPackage\Abstracts;

use Illuminate\Http\Client\Response;
use Unlu\PaymentPackage\Contracts\IPaymentGatewayResponse;
use Unlu\PaymentPackage\Exceptions\InvalidResponseException;

abstract class PaymentGatewayResponse implements IPaymentGatewayResponse
{

    /**
     * @var Response
     */
    protected Response $response;

    /**
     * @param Response $response
     */
    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    /**
     * @return int
     */
    public function getHttpStatusCode(): int
    {
        return $this->response->status();
    }

    /**
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
     * @return array
     */
    public function toArray(): array
    {
        return $this->response->json();
    }

    /**
     * @return bool
     */
    abstract public function isSuccess(): bool;
}
