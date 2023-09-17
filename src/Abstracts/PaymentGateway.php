<?php

namespace Unlu\PaymentPackage\Abstracts;


use Illuminate\Http\Client\PendingRequest;
use Unlu\PaymentPackage\Contracts\IPaymentGateWay;
use Unlu\PaymentPackage\Contracts\IPaymentGatewayResponse;
use Unlu\PaymentPackage\Contracts\IPaymentPayload;

abstract class PaymentGateway implements IPaymentGateWay
{

    /**
     * @var string
     */
    protected string $authToken;

    /**
     * @var IPaymentPayload
     */
    protected IPaymentPayload $payload;

    /**
     * @var PendingRequest
     */
    protected PendingRequest $client;

    /**
     * @param IPaymentPayload $payload
     * @param PendingRequest $client
     */
    public function __construct(IPaymentPayload $payload, PendingRequest $client)
    {
        $this->client = $client;
        $this->payload = $payload;
        $this->authToken = $this->getAuthToken();
    }

    /**
     * @return string
     */
    abstract protected function getAuthToken(): string;

    /**
     * @param array $params
     * @return IPaymentGatewayResponse
     */
    abstract public function payWith3D(array $params): IPaymentGatewayResponse;
}
