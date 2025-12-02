<?php

namespace App\Services;

use ZarinPal\Sdk\ClientBuilder;
use ZarinPal\Sdk\Options;
use ZarinPal\Sdk\ZarinPal;

use ZarinPal\Sdk\Endpoint\PaymentGateway\RequestTypes\RequestRequest;
use ZarinPal\Sdk\Endpoint\PaymentGateway\RequestTypes\VerifyRequest;
use ZarinPal\Sdk\Endpoint\PaymentGateway\RequestTypes\InquiryRequest;

use Http\Client\Common\Plugin\HeaderDefaultsPlugin;
use ZarinPal\Sdk\HttpClient\Exception\ResponseException;

class ZarinpalService
{
    protected $paymentGateway;

    public function __construct()
    {
        $clientBuilder = new ClientBuilder();
        $clientBuilder->addPlugin(new HeaderDefaultsPlugin([
            'Accept' => 'application/json',
        ]));

        $options = new Options([
            'client_builder' => $clientBuilder,
            'sandbox' => env('ZARINPAL_SANDBOX', false),
            'merchant_id' =>setting('merchant_id'),
        ]);

        $zarinpal = new ZarinPal($options);
        $this->paymentGateway = $zarinpal->paymentGateway();
    }

    
    public function requestPayment(array $data)
    {
        $req = new RequestRequest();

        $req->amount        = $data['amount'];
        $req->description   = $data['description'];
        $req->callback_url  = $data['callback_url'];

        $req->mobile        = $data['mobile'] ?? null;
        $req->email         = $data['email'] ?? null;
        $req->currency      = $data['currency'] ?? 'IRR';
        $req->referrer_id   = $data['referrer_id'] ?? null;
        $req->cardPan       = $data['cardPan'] ?? null;
        $req->wages         = $data['wages'] ?? null;

        try {
            $response = $this->paymentGateway->request($req);

            return [
                'success' => true,
                'authority' => $response->authority,
                'payment_url' => $this->paymentGateway->getRedirectUrl($response->authority)
            ];

        } catch (ResponseException $e) {

            return [
                'success' => false,
                'message' => $e->getErrorDetails()
            ];

        } catch (\Exception $e) {

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    
    public function verifyPayment($authority, $amount)
    {
        $verifyReq = new VerifyRequest();
        $verifyReq->authority = $authority;
        $verifyReq->amount = $amount;

        try {
            $response = $this->paymentGateway->verify($verifyReq);

            return [
                'success' => ($response->code === 100 || $response->code === 101),
                'code' => $response->code,
                'ref_id' => $response->ref_id ?? null,
                'card_pan' => $response->card_pan ?? null,
                'fee' => $response->fee ?? null,
                'raw' => $response
            ];

        } catch (ResponseException $e) {
            return [
                'success' => false,
                'message' => $e->getErrorDetails()
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    
    public function inquiryPayment($authority)
    {
        $inquiryReq = new InquiryRequest();
        $inquiryReq->authority = $authority;

        try {
            $response = $this->paymentGateway->inquiry($inquiryReq);

            return [
                'success' => true,
                'code' => $response->code,
                'message' => $response->message,
                'status' => $response->status,
                'raw' => $response
            ];

        } catch (ResponseException $e) {
            return [
                'success' => false,
                'message' => $e->getErrorDetails()
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
