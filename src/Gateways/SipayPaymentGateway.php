<?php

namespace Unlu\PaymentPackage\Gateways;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Cache;
use Unlu\PaymentPackage\Abstracts\PaymentGateway;
use Unlu\PaymentPackage\Contracts\Installable;
use Unlu\PaymentPackage\Contracts\PaymentGatewayResponse;
use Unlu\PaymentPackage\Contracts\Refundable;
use Unlu\PaymentPackage\Contracts\Walletable;
use Unlu\PaymentPackage\Payloads\SipayPayload;
use Unlu\PaymentPackage\Responses\SipayResponse;
use Unlu\PaymentPackage\Exceptions\AuthTokenException;

class SipayPaymentGateway extends PaymentGateway implements Walletable, Refundable, Installable
{
    /**
     * @var string
     */
    private string $appSecret;

    /**
     * @var string
     */
    private string $appKey;

    /**
     * @var SipayPayload
     */
    protected SipayPayload $payload;

    /**
     * @var PendingRequest
     */
    protected PendingRequest $client;

    public function __construct(SipayPayload $payload, PendingRequest $client, $appSecret, $appKey)
    {
        $this->client = $client;
        $this->appSecret = $appSecret;
        $this->appKey = $appKey;
        $this->payload = $payload;
        parent::__construct();
    }

    /**
     * Get credit cards resource for given customer
     *
     * @param  array $params
     * @return SipayResponse
     */
    public function getCards(array $params): SipayResponse
    {
        $response = $this->client->acceptJson()->withToken($this->authToken)
            ->withBody(json_encode($this->payload->setData($params)->toArray()), 'application/json')
            ->get('ccpayment/api/getCardTokens');

        return new SipayResponse($response);
    }

    /**
     * Save credit cards on sipay host
     *
     * @param  array $params
     * @return SipayResponse
     */
    public function saveCard(array $params): SipayResponse {
        $response = $this->client->acceptJson()->asJson()->withToken($this->authToken)
            ->post('ccpayment/api/saveCard', $this->payload->setData($params)->addHashKey('saveCard')->toArray());

        return new SipayResponse($response);
    }

    /**
     * Update credit cards on sipay host
     *
     * @param array $params
     * @return SipayResponse
     */
    public function updateCard(array $params): SipayResponse {
        $response = $this->client->acceptJson()->asJson()->withToken($this->authToken)
            ->post('ccpayment/api/editCard', $this->payload->setData($params)->addHashKey('updateCard')->toArray());

        return new SipayResponse($response);
    }

    /**
     * Delete stored credit cards for given customer on sipay host
     *
     * @param  array $params
     * @return SipayResponse
     */
    public function deleteCard(array $params): SipayResponse
    {
        $response = $this->client->acceptJson()->asJson()->withToken($this->authToken)
            ->post('ccpayment/api/deleteCard', $this->payload->setData($params)->addHashKey('deleteCard')->toArray());

        return new SipayResponse($response);
    }

    /**
     * Make payment with 3DS method
     *
     * @param  array $params
     * @return SipayResponse
     */
    public function payWith3D(array $params): SipayResponse
    {
        $response = $this->client->acceptJson()->asJson()->withToken($this->authToken)
            ->post('ccpayment/api/paySmart3D', $this->payload->setData($params)->addHashKey('payment')->toArray());

        return new SipayResponse($response);
    }

    /**
     * Make payment by stored credit card token
     *
     * @param  array $params
     * @return SipayResponse
     */
    public function payWithSavedCard(array $params): SipayResponse {
        $response = $this->client->acceptJson()->asJson()->withToken($this->authToken)
            ->post('ccpayment/api/payByCardToken', $this->payload->setData($params)->addHashKey('payment')->toArray());

        return new SipayResponse($response);
    }

    /**
     * Request refund for paid invoice
     *
     * @param  array $params
     * @return SipayResponse
     */
    public function refund(array $params): SipayResponse
    {
        $response = $this->client->acceptJson()->asJson()->withToken($this->authToken)
            ->post('ccpayment/api/refund', $this->payload->setData($params)->addHashKey('refund')->toArray());

        return new SipayResponse($response);
    }

    /**
     * Get authentication bearer token
     *
     * @return string
     * @throws AuthTokenException
     */
    protected function getAuthToken(): string
    {
        if (is_null(Cache::get('sipay_token'))) {
            $response = $this->client->acceptJson()->contentType('application/json')->post('ccpayment/api/token', [
                    'app_id' => $this->appKey,
                    'app_secret' => $this->appSecret,
                ]);
            if (!$response->successful() || !$response->json('data.token')) throw new AuthTokenException($response->body());
            Cache::put('sipay_token', $response->json('data.token'), $response->json('data.expires_at'));
        }

        return Cache::get('sipay_token');
    }

    /**
     * Make a request to inquiry installment for given credit card
     *
     * @param array $params
     * @return PaymentGatewayResponse
     */
    public function installmentInquiry(array $params): PaymentGatewayResponse
    {
        $response = $this->client->acceptJson()->asJson()->withToken($this->authToken)
            ->post('ccpayment/api/getpos', $this->payload->setData($params)->toArray());

        return new SipayResponse($response);
    }
}
