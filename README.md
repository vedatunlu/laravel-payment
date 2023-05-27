<p align="center">
    <img src="https://img.shields.io/packagist/v/vedatunlu/payment" alt="Latest version">
    <img src="https://img.shields.io/badge/licence-MIT-green" alt="Licence">
</p>

# Laravel Payment

Laravel Payment is a package used for managing payment gateway integrations. This package provides an easy interface for
integrating popular payment gateways such as Sipay (and soon Iyzico, Sipay, PayTr, iPara, Paynet etc.) into your Laravel
application.

## Features

- Easy integration with various payment gateways
- Easy usage with Payment client class
- Wallet usage with available payment gateways
- 

## Installation

1. Use Composer to add the Laravel Payment package to your project:

```bash
    composer require vedatunlu/payment
```

2. Package service providers will be discovered by your laravel project automatically. So you don't need to update your
   app/config.php file to add package service provider.

3. Publish the config files to your project.

```bash
    php artisan vendor:publish --tag=payment-config
```

The command above will create a file named payment.php on config directory.

### Create environment variables on your .env file before usage

Add your credentials provided by your payment gateway to the related payment gateway scope of the payment config file which is published
to the config directory of your laravel file.
You can provide the credentials from your service provider if you don't have yet. You don't need to define any other
credentials if you don't plan to use another one. If yes. You can use each of payment gateway supported by the package
by defining credentials on the related scope of the payment config file.

## Payment class usage reference table

Please check out the table given below to get basic knowledge of the Payment class behavior before using it.

<table>
    <tr>
        <td><b>Method Name</b></td>
        <td><b>Description</b></td>
        <td><b>Available gateways</b></td>
    </tr>
    <tr>
        <td>getCards</td>
        <td>Returns saved cards with given customer</td>
        <td>Sipay</td>
    </tr>
    <tr>
        <td>saveCard</td>
        <td>Stores card information on the payment gateway host</td>
        <td>Sipay</td>
    </tr>
    <tr>
        <td>updateCard</td>
        <td>Updates card information on the payment gateway host</td>
        <td>Sipay</td>
    </tr>
    <tr>
        <td>deleteCard</td>
        <td>Deletes card information on the payment gateway host</td>
        <td>Sipay</td>
    </tr>
    <tr>
        <td>payWith2D</td>
        <td>Start a 2D payment with given credit card for given customer info</td>
        <td>Sipay</td>
    </tr>
    <tr>
        <td>payWith3D</td>
        <td>Start a 3D payment with given credit card for given customer info</td>
        <td>Sipay</td>
    </tr>
    <tr>
        <td>payWithSavedCard</td>
        <td>Start a payment with stored credit card on the payment gateway host</td>
        <td>Sipay</td>
    </tr>
    <tr>
        <td>verifyPayment</td>
        <td>Make a verification request for given payment</td>
        <td>Sipay</td>
    </tr>
    <tr>
        <td>refund</td>
        <td>Start a refund process for given invoice</td>
        <td>Sipay</td>
    </tr>
    <tr>
        <td>installmentInquiry</td>
        <td>Make an installment inquiry request for given credit card</td>
        <td>Sipay</td>
    </tr>
    <tr>
        <td>transactionStatus</td>
        <td>Check all processed transaction's status</td>
        <td>Sipay</td>
    </tr>
</table>

> Hint: All methods accepts an array as parameter. You are not required to attach any credentials such as merchant_id, app_key, secret_key thanks to the package service provider can attach the credentials automatically. Please check your payment gateway documentation to get further information about the required parameters of the endpoints.

# Basic Usage Examples

1. getCards:

```php
    // will return credit card resources
    Payment::gateway('sipay')
        ->getCards([
            'customer_number' => 123123
        ])->toArray();
```

2. saveCard:

```php
    // will return credit card token
    Payment::gateway('sipay')
        ->saveCard([
            'card_number' => 4508034508034509,
            'customer_number' => 123123,
            'expiry_month' => 12,
            'expiry_year' => 2026,
            'card_holder_name' => 'Vedat Ünlü'
        ])->toArray();
```

3. payWith3D:

```php
    // will return a html form body and this form will redirect to the 3D verification
    $response = Payment::gateway('sipay')
                    ->payWith3D([
                        'cc_holder_name' => 'Vedat Ünlü',
                        'cc_no' => '4508034508034509',
                        'expiry_month' => '12',
                        'expiry_year' => '2026',
                        'cvv' => '000',
                        'currency_code' => 'TRY',
                        'installments_number' => 1,
                        'invoice_id' => rand(100000, 999999),
                        'invoice_description' => 'invoice_description',
                        'name' => 'Vedat',
                        'surname' => 'Ünlü',
                        'total' => 101.10,
                        'items' => json_encode([
                            [
                                'name' => 'Item 2',
                                'price' => 101.10,
                                'quantity' => 1,
                                'description' => "item description"
                            ]
                        ]),
                        'cancel_url' => 'payment-success-callback-url', // route('payment.callback.success)
                        'return_url' => 'payment-error-callback-url', // route('payment.callback.error)
                        'response_method' => 'POST'
                    ]);
                    
    if ($response->isSuccess() == true) {
        return $response->get3DSForm(); // get response as html form
    }
    
    return $response->toArray(); // get response as array
```

4. Validate Incoming Sipay Hash key:

You can easily validate hash keys returned from sipay gateway using SipayHashKeyValidator class.

```php
    SipayHashKeyValidator::validateHashKey($hashKey, $appSecret);
```

This method returns array including status, total amount, invoice id, order id, currency code if key is valid. If not method will be returned false.

# Contributing to the package

We welcome and appreciate your contributions to the package! The contribution guide can be found [here](https://github.com/vedatunlu/laravel-payment/blob/master/CONTRIBUE.md).

## License

This package is open-sourced software licensed under the [MIT license](https://github.com/vedatunlu/laravel-payment/blob/master/LICENSE).
