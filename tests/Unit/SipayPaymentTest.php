<?php

namespace Unlu\PaymentPackage\Tests\Unit;

use Illuminate\Support\Facades\Http;
use Unlu\PaymentPackage\Exceptions\InvalidGatewayException;
use Unlu\PaymentPackage\Payment;
use Unlu\PaymentPackage\Tests\TestCase;

class SipayPaymentTest extends TestCase
{
    protected $baseUrl;

    public function setUp(): void
    {
        parent::setUp();
        $this->baseUrl = config('payment.sipay.credentials.host');
        //$this->mockResponse('auth_response.json', 'ccpayment/api/token');
    }

    private function mockResponse(string $fileName, $path): void
    {
        Http::fake([
            $this->baseUrl . '/' . $path => Http::response($this->jsonToArray($fileName), 200)
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
                'card_token' => '4a1ef584c391ef0e4246cd758ef3a325',
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
                'card_token' => '4a1ef584c391ef0e4246cd758ef3a325',
                'customer_number' => 123123
            ]);
        $this->assertEquals($this->jsonToArray('delete_cards_response.json'), $response->toArray());
    }

    public function test_pay_with_3DS_method()
    {
        Http::fake([
            $this->baseUrl.'/ccpayment/api/paySmart3D' => Http::response("<form>form</form>", 200)
        ]);
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
                'cancel_url' => 'http://localhost:8000/fail',
                'return_url' => 'http://localhost:8000/success',
                'response_method' => 'POST'
            ]);

        $this->assertStringContainsString('form', $response->get3DSForm());
    }

    public function test_pay_with_saved_card_method()
    {
        Http::fake([
            $this->baseUrl.'/ccpayment/api/payByCardToken' => Http::response("<form>form</form>", 200)
        ]);
        $response = Payment::gateway('sipay')
            ->payWithSavedCard([
                'card_token' => '448335b16c6a3b6bc04aca61cdc55ae5sdf',
                'customer_number' => 123123,
                'customer_name' => 'Vedat Ünlü',
                'customer_email' => 'vedatunlu10@gmail.com',
                'customer_phone' => '+905532054660',
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

    public function test_installment_inquiry_method()
    {
        $this->mockResponse('installment_inquiry_response.json', 'ccpayment/api/getpos');
        $response = Payment::gateway('sipay')
            ->installmentInquiry([
                "credit_card" => "534261",
                "amount" => 248.5,
                "currency_code" => "TRY",
                "is_recurring" => 0,
                "is_2d" => 0
            ]);
        $this->assertEquals($this->jsonToArray('installment_inquiry_response.json'), $response->toArray());
    }

    public function test_pay_with_2D_method()
    {
        $this->mockResponse('pay_with_2D_response.json', 'ccpayment/api/paySmart2D');
        $response = Payment::gateway('sipay')
            ->payWith2D([
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
                'return_url' => 'http://localhost:8000/success'
            ]);
        $this->assertEquals($this->jsonToArray('pay_with_2D_response.json'), $response->toArray());
    }

    public function test_verify_payment_method()
    {
        $this->mockResponse('verify_payment_response.json', 'ccpayment/api/confirmPayment');
        $response = Payment::gateway('sipay')
            ->verifyPayment([
                "invoice_id" => "Cs2Ghy621dsa42f1D2",
                "status" => 1,
                "total" => 10.25
            ]);
        $this->assertEquals($this->jsonToArray('verify_payment_response.json'), $response->toArray());
    }

    public function test_transaction_status_method()
    {
        $this->mockResponse('transaction_status_response.json', 'ccpayment/api/checkStatus');
        $response = Payment::gateway('sipay')
            ->transactionStatus([
                "invoice_id" => "Cs2Ghy621dsa42f1D2",
                "include_pending_status" => true,
            ]);
        $this->assertEquals($this->jsonToArray('transaction_status_response.json'), $response->toArray());
    }

    public function test_payment_factory_with_invalid_gateway()
    {
        $this->withoutExceptionHandling();
        $this->assertThrows(fn() => Payment::gateway('test'), InvalidGatewayException::class);
    }
}
