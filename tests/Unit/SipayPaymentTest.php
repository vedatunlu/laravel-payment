<?php

namespace Unlu\PaymentPackage\Tests\Unit;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Unlu\PaymentPackage\Payment;
use Unlu\PaymentPackage\Responses\SipayResponse;
use Unlu\PaymentPackage\Tests\TestCase;

class SipayPaymentTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Http::fake([
            '*' => Http::response(file_get_contents(__DIR__ . '/../FakeResponses/Sipay/auth_success.json')
                , 200, ['Content-Type' => 'application/json'])
        ]);
    }

    public function test_get_card_list_method()
    {
        Http::fake([
            '*' => Http::response([
                "status_code" => 100,
                "status_description" => "Successfully Generated token",
                "data" => [
                    "token" => "bsdhg2ı4tsdhfalshdfsdgf23fjhsagdkjsdhgoa",
                    "is_3d" => 1,
                    "expires_at" => "2030-12-12 12:12:12"
                ]
            ], 200, ['Content-Type' => 'application/json'])
        ]);
        $response = Payment::gateway('sipay')
            ->getCards(['customer_number' => 123123]);
        dd($response);
        $this->assertEquals(100, $response->getStatusCode());
    }

    public function test_store_credit_card_method()
    {
        $response = $this->storeCreditCards();
        $this->assertEquals(100, $response->getStatusCode());
    }

    public function test_update_credit_card_method()
    {
        $cardToken = $this->storeCreditCards()->toArray()['card_token'];
        $response = $this->updateCreditCards($cardToken);
        $this->assertEquals(100, $response->getStatusCode());
    }

    public function test_delete_credit_card_method()
    {
        $cardToken = $this->storeCreditCards()->toArray()['card_token'];
        $response = Payment::gateway('sipay')->deleteCard([
                'card_token' => $this->$cardToken,
                'customer_number' => 123123
            ]);
        $this->assertEquals(100, $response->getStatusCode());
    }


    /*public function test_pay_with_3DS_with_success_response()
    {
        $this->sipayPayWithDsSuccessResponse();
        $response = Payment::channel(PaymentChannel::SIPAY)
            ->setParams([
                'cc_holder_name' => 'Vedat Ünlü',
                'cc_no' => '4508034508034509',
                'expiry_month' => '12',
                'expiry_year' => '2026',
                'cvv' => '000',
                'currency_code' => 'TRY',
                'installments_number' => 1,
                'invoice_id' => rand(100000,999999),
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
                'cancel_url' => 'http://localhost:8000/fail',
                'return_url' => 'http://localhost:8000/success',
                'response_method' => 'POST'
            ])->payWith3DS();

        $this->assertStringContainsString('form', $response->get3DSForm());
    }

    public function test_pay_with_3DS_with_failed_response()
    {
        $this->withoutExceptionHandling();
        $this->sipayPayWithDsFailedResponse();
        $this->assertThrows(
            fn() => Payment::channel(PaymentChannel::SIPAY)
                ->setParams([
                    'cc_holder_name' => 'Vedat Ünlü',
                    'cc_no' => '4508034508034509',
                    'expiry_month' => '12',
                    'expiry_year' => '2026',
                    'cvv' => '000',
                    'currency_code' => 'TRY',
                    'installments_number' => 1,
                    'invoice_id' => rand(100000,999999),
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
                    'cancel_url' => 'http://localhost:8000/fail',
                    'return_url' => 'http://localhost:8000/success',
                    'response_method' => 'POST'
                ])->payWith3DS(),
            PayWith3DSException::class
        );
    }

    public function test_pay_with_saved_card_with_success_response()
    {
        //$this->sipaySaveCardsSuccessResponse();
        $response = Payment::channel(PaymentChannel::SIPAY)
            ->setParams([
                'card_token' => '6a7a5d5184cf8fd6d6cbb59e1bc5539f',
                'currency_code' => 'TRY',
                'installments_number' => 1,
                'invoice_id' => rand(100000,999999),
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
                'cancel_url' => 'http://localhost:8000/fail',
                'return_url' => 'http://localhost:8000/success',
                'response_method' => 'POST'
            ])->payWithSavedCard();
        dd($response);
    }*/
}
