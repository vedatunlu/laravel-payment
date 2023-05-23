<?php

namespace  Unlu\PaymentPackage\Contracts;

interface PaymentGateway
{

    /**
     * @param  array<string , mixed>  $params
     * @return PaymentGatewayResponse
     */
    public function getCards(array $params): PaymentGatewayResponse;

    /**
     * @param  array<string , mixed>  $params
     * @return PaymentGatewayResponse
     */
    public function saveCard(array $params): PaymentGatewayResponse;

    /**
     * @param  array<string , mixed>  $params
     * @return PaymentGatewayResponse
     */
    public function updateCard(array $params): PaymentGatewayResponse;

    /**
     * @param  array<string , mixed>  $params
     * @return PaymentGatewayResponse
     */
    public function deleteCard(array $params): PaymentGatewayResponse;

    /**
     * @param  array<string , mixed>  $params
     * @return PaymentGatewayResponse
     */
    public function payWith3DS(array $params): PaymentGatewayResponse;

    /**
     * @param  array<string , mixed>  $params
     * @return PaymentGatewayResponse
     */
    public function payWithSavedCard(array $params): PaymentGatewayResponse;

    /**
     * @param  array<string , mixed>  $params
     * @return PaymentGatewayResponse
     */
    public function refund(array $params): PaymentGatewayResponse;
}
