<?php

namespace Unlu\PaymentPackage\Gateways;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Cache;
use Unlu\PaymentPackage\Contracts\PaymentGateway;
use Unlu\PaymentPackage\Payloads\SipayPayload;
use Unlu\PaymentPackage\Responses\SipayResponse;
use Unlu\PaymentPackage\Exceptions\AuthTokenException;

class SipayPaymentGateway implements PaymentGateway
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
     * @var string
     */
    private string $bearerToken;

    /**
     * @var SipayPayload
     */
    protected SipayPayload $payload;

    /**
     * @var PendingRequest
     */
    protected PendingRequest $client;

    /**
     * @throws AuthTokenException
     */
    public function __construct(SipayPayload $payload, PendingRequest $client, $appSecret, $appKey)
    {
        $this->appSecret = $appSecret;
        $this->appKey = $appKey;
        $this->payload = $payload;
        $this->client = $client;
        $this->bearerToken = $this->getAuthToken();
    }

    /**
     * Get credit cards resource for given customer
     *
     * @param  array $params
     * @return SipayResponse
     */
    public function getCards(array $params): SipayResponse
    {
        $response = $this->client->acceptJson()->withToken($this->bearerToken)
            ->withBody(json_encode($this->payload->setData($params)->toArray()), 'application/json')
            ->get('ccpayment/api/getCardTokens');

        return new SipayResponse($response);
    }

    /**
     * @param  array $params
     * @return SipayResponse
     */
    public function saveCard(array $params): SipayResponse {
        $response = $this->client->acceptJson()->asJson()->withToken($this->bearerToken)
            ->post('ccpayment/api/saveCard', $this->payload->setData($params)->addSaveCardHashKey()->toArray());

        return new SipayResponse($response);
    }

    /**
     * @param  array $params
     * @return SipayResponse
     */
    public function updateCard(array $params): SipayResponse {
        $response = $this->client->acceptJson()->asJson()->withToken($this->bearerToken)
            ->post('ccpayment/api/editCard', $this->payload->setData($params)->addUpdateCardHashKey()->toArray());

        return new SipayResponse($response);
    }

    /**
     * Delete stored credit cards for given customer
     *
     * @param  array $params
     * @return SipayResponse
     */
    public function deleteCard(array $params): SipayResponse
    {
        $response = $this->client->acceptJson()->asJson()->withToken($this->bearerToken)
            ->post('ccpayment/api/deleteCard', $this->payload->setData($params)->addDeleteCardHashKey()->toArray());

        return new SipayResponse($response);
    }

    /**
     * Make payment with 3DS method
     *
     * @param  array $params
     * @return SipayResponse
     */
    public function payWith3DS(array $params): SipayResponse
    {
        $response = $this->client->acceptJson()->asJson()->withToken($this->bearerToken)
            ->post('ccpayment/api/paySmart3D', $this->payload->setData($params)->addPaymentHashKey()->toArray());

        return new SipayResponse($response);
    }

    /**
     * Make payment by stored credit card token
     *
     * @param  array $params
     * @return SipayResponse
     */
    public function payWithSavedCard(array $params): SipayResponse {
        $response = $this->client->acceptJson()->asJson()->withToken($this->bearerToken)
            ->post('ccpayment/api/payByCardToken', $this->payload->setData($params)->addPaymentHashKey()->toArray());

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
        $response = $this->client->acceptJson()->asJson()->withToken($this->bearerToken)
            ->post('ccpayment/api/refund', $this->payload->setData($params)->addRefundHashKey()->toArray());

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
            if (!$response->successful()) throw new AuthTokenException($response->body());
            Cache::put('sipay_token', $response->json('data.token'), $response->json('data.expires_at'));
        }

        return Cache::get('sipay_token');
    }
}
