<?php

namespace Unlu\PaymentPackage\Contracts;

interface Walletable
{
    public function getCards(array $params): IPaymentGatewayResponse;

    public function saveCard(array $params): IPaymentGatewayResponse;

    public function updateCard(array $params): IPaymentGatewayResponse;

    public function deleteCard(array $params): IPaymentGatewayResponse;

    public function payWithSavedCard(array $params): IPaymentGatewayResponse;
}
