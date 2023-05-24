<?php

namespace Unlu\PaymentPackage\Tests\Unit;

use Illuminate\Support\Facades\Http;
use Unlu\PaymentPackage\Exceptions\InvalidGatewayException;
use Unlu\PaymentPackage\Payment;
use Unlu\PaymentPackage\Tests\TestCase;

class SipayPaymentTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->mockResponse('auth_response.json', 'ccpayment/api/token');
    }

    private function mockResponse(string $fileName, $path): void
    {
        Http::fake([
            config('sipay.credentials.host').'/'.$path => Http::response($this->jsonToArray($fileName), 200)
        ]);
    }

    private function jsonToArray(string $fileName): array
    {
        return json_decode(file_get_contents(__DIR__ . '/../FakeResponses/Sipay/' . $fileName), true);
    }

    public function test_get_card_list_method()
    {
        $this->mockResponse('get_cards_response.json', 'ccpayment/api/getCardTokens');
        $response = Payment::gateway('sipay')
            ->getCards(['customer_number' => 123123]);
        $this->assertEquals($this->jsonToArray('get_cards_response.json'), $response->toArray());
    }

    public function test_store_credit_card_method()
    {
        $this->mockResponse('save_cards_response.json', 'ccpayment/api/saveCard');
        $response = Payment::gateway('sipay')
            ->saveCard([
                'card_number' => 4508034508034509,
                'customer_number' => 123123,
                'expiry_month' => 12,
                'expiry_year' => 2026,
                'card_holder_name' => 'Vedat Ünlü'
            ]);
        $this->assertEquals($this->jsonToArray('save_cards_response.json'), $response->toArray());
    }

    public function test_update_credit_card_method()
    {
        $this->mockResponse('update_cards_response.json', 'ccpayment/api/editCard');
        $response = Payment::gateway('sipay')
            ->updateCard([
                'card_token' => 'WNPDZDQNMVNRQO23IAHKDKXIWCRGWHEXCFNRDXXK5CMXGM5A',
                'customer_number' => 123123,
                'expiry_month' => 12,
                'expiry_year' => 2026,
                'card_holder_name' => 'Vedat Ünlü'
            ]);
        $this->assertEquals($this->jsonToArray('update_cards_response.json'), $response->toArray());
    }

    public function test_delete_credit_card_method()
    {
        $this->mockResponse('delete_cards_response.json', 'ccpayment/api/deleteCard');
        $response = Payment::gateway('sipay')
            ->deleteCard([
                'card_token' => 'WNPDZDQNMVNRQO23IAHKDKXIWCRGWHEXCFNRDXXK5CMXGM5A',
                'customer_number' => 123123
            ]);
        $this->assertEquals($this->jsonToArray('delete_cards_response.json'), $response->toArray());
    }

    public function test_pay_with_3DS_method()
    {
        Http::fake([
            '*' => Http::response("<form>form</form>", 200)
        ]);
        $response = Payment::gateway('sipay')
            ->payWith3DS([
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
            'cancel_url' => 'http://localhost:8000/fail',
            'return_url' => 'http://localhost:8000/success',
            'response_method' => 'POST'
        ]);

        $this->assertStringContainsString('form', $response->get3DSForm());
    }

    public function test_pay_with_saved_card_method()
    {
        Http::fake([
            config('sipay.credentials.host').'api/payByCardToken' => Http::response("<form>form</form>", 200)
        ]);
        $response = Payment::gateway('sipay')
            ->payWithSavedCard([
                'card_token' => 'WNPDZDQNMVNRQO23IAHKDKXIWCRGWHEXCFNRDXXK5CMXGM5A',
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
                'cancel_url' => 'http://localhost:8000/fail',
                'return_url' => 'http://localhost:8000/success',
                'response_method' => 'POST'
            ]);

        $this->assertStringContainsString('form', $response->get3DSForm());
    }

    public function test_refund_method()
    {
        $this->mockResponse('refund_response.json', 'ccpayment/api/refund');
        $response = Payment::gateway('sipay')
            ->refund([
                'invoice_id' => 'Cs2Ghy621dsa42f1D2',
                'amount' => 123,
                'refund_transaction_id' => 's2345g54h3ıh'
            ]);
        $this->assertEquals($this->jsonToArray('refund_response.json'), $response->toArray());
    }

    public function test_payment_factory_with_invalid_gateway()
    {
        $this->withoutExceptionHandling();
        $this->assertThrows(fn () => Payment::gateway('test'), InvalidGatewayException::class);
    }
}
