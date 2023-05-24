<?php

namespace  Unlu\PaymentPackage\Contracts;

interface PaymentGateway
{
    public function getCards(array $params): PaymentGatewayResponse;

    public function saveCard(array $params): PaymentGatewayResponse;

    public function updateCard(array $params): PaymentGatewayResponse;

    public function deleteCard(array $params): PaymentGatewayResponse;

    public function payWith3DS(array $params): PaymentGatewayResponse;

    public function payWithSavedCard(array $params): PaymentGatewayResponse;

    public function refund(array $params): PaymentGatewayResponse;
}
